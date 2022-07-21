<?PHP
global $instance;
class TntdbCache {    
    private $ds = DIRECTORY_SEPARATOR;
    private function __clone(){}
    private function __wakeup(){}
    private $cacheMap = array();
    private $cacheChanged = FALSE;
    const DEFAULT_CACHE_TIMEOUT_SECONDS = 3600;
    public function getInstance(){ return $this; }
    public function isCreated(){ return $GLOBALS['instance'] !== NULL; }
    private function getCacheTimeout(){ return self::DEFAULT_CACHE_TIMEOUT_SECONDS;}
    public function isChanged(){ return $this->cacheChanged;}
    public function __construct(){ $this->loadCacheFromFile(); }
    public function clear(){
        $dirPath = $this->getCacheDirPath();
        $files = scandir($dirPath);
        foreach ($files as $filePath) {
            if ($filePath == '.' || $filePath == '..') continue;
            unlink($dirPath.$filePath);
        }
        rmdir($dirPath);
    }
    public function getCacheDirPath() {
        $dirPath = realpath(DIR_SYSTEM). $this->ds.'speedup_cache'.$this->ds.'db_cache'.$this->ds;
        if (!file_exists($dirPath)) mkdir($dirPath);
        return $dirPath;
    }
    private function getCacheMapFilePath() {
        $dir = DIR_DOWNLOAD . 'db_cache/';
        $dbCacheFilePath = $dir.'db_cache.dat';
        if (file_exists($dbCacheFilePath)) unlink($dbCacheFilePath);
        if (file_exists($dir)) rmdir($dir);
        $latestPath = $this->getCacheDirPath();
        $latestPath .= '_cache';
        return $latestPath;
    }
    public function loadCacheFromFile() {
        $cacheFilePath = $this->getCacheMapFilePath();
        if (!file_exists($cacheFilePath)) return;
        $handle = fopen($cacheFilePath, "r");
        flock($handle, LOCK_SH);
        $cacheSerialized = fread($handle, filesize($cacheFilePath));
        fclose($handle);
        $this->cachemap = unserialize($cacheSerialized);
    }
    public function saveCacheToFile() {
        $cacheFilePath = $this->getCacheMapFilePath();
        $cacheSerialized = serialize($this->cachemap);
        $handle = fopen($cacheFilePath, 'w');
        flock($handle, LOCK_EX);
        fwrite($handle, $cacheSerialized);
        fflush($handle);
        fclose($handle);
    }
    public function addSelectFetchToCache($queryText, $fetchData){ $this->setCacheEntry($queryText, $fetchData); }
    public function getCachedSelectFetch($queryText) {
        $cachedFetchData = $this->getCachedDataFromCacheMap($queryText);
        return $cachedFetchData;
    }
    private function getCachedDataFromCacheMap($queryText) {   
        if (isset($this->cachemap[$queryText]) && $this->cachemap[$queryText] != null) {
            $cachedEntry = $this->cachemap[$queryText];
            $cacheTime = $cachedEntry['time'];
            $nowTime = date_create();
            $secondsDiffSpan = date_diff($cacheTime, $nowTime);
            $daysDiffCount = $secondsDiffSpan->format('%a');
            if ($daysDiffCount >= self::getCacheTimeout()) {
                $this->removeCacheEntry($queryText);
                return null;
            }
            return $cachedEntry['data'];
        }
        return null;
    }
    public function processModificationQuery($queryText) {
        $dbTableNamesInQuery = $this->extractDbTableNamesFromQueryText($queryText);
        foreach ($this->cachemap as $queryTextKey => $cacheEntry) {
            foreach ($dbTableNamesInQuery as $dbTableName) { 
                if (stripos($queryTextKey, $dbTableName)) {
                    $this->removeCacheEntry($queryTextKey);
                }   
            }
        }
    }
    private function getCacheEntryFilePath($cacheKey){ return $this->getCacheDirPath().$this->getCacheEntryFileNameByHash($this->getSelectQueryHash($cacheKey));}
    private function getCacheEntryFileNameByHash($hash){ return $hash; }
    private function getCacheEntryData($cacheKey){
        if (!file_exists($this->getCacheEntryFilePath($cacheKey))) return NULL;
        $nowTime = date_create();
        $timeModified = date_create();
        date_timestamp_set($timeModified, filemtime($this->getCacheEntryFilePath($cacheKey)));
        $secondsDiffSpan = date_diff($timeModified, $nowTime);
        $daysDiffCount = $secondsDiffSpan->format('%a');
        if ($daysDiffCount >= self::getCacheTimeout()) {
            $this->removeCacheEntry($cacheKey);
            return NULL;
        }
        return unserialize(file_get_contents($this->getCacheEntryFilePath($cacheKey)));
    }
    private function removeCacheEntry($queryTextKey) {
        if (isset($this->cachemap[$queryTextKey])) {
            $this->cachemap[$queryTextKey] = null;
            unset($this->cachemap[$queryTextKey]);
            $this->cacheChanged = TRUE;
        }
    }
    private function getSelectQueryHash($queryText){ return md5($queryText); }
    private function setCacheEntry($cacheKey, $cacheData) {
        $cachedTime = date_create();
        $this->cachemap[$cacheKey] = array('time' => $cachedTime, 'data' => $cacheData);
        $this->cacheChanged = TRUE;
    }
    public function isModificationQuery($queryText) {
        $arReadQueries = array('select', 'show tables', 'show columns');
        foreach ($arReadQueries as $queryRead) {
            $striposSelect = stripos(trim($queryText), $queryRead);
            if ($striposSelect === 0 || $striposSelect === '0') return FALSE;
        }
        return TRUE;
    }
    public function extractDbTableNamesFromQueryText($queryText) {
        $tableNames = preg_grep('/'.DB_PREFIX.'.+/', explode(' ', $queryText));
        return $tableNames;
    }
    public function processDbQuery($db, $sql, $params = NULL) {
        $config = $GLOBALS['registry']->get('config');
        if(defined('HTTP_CATALOG') || !$config->get('speedup_status') || !$config->get('speedup_db_cache')) return $db->queryNonCache($sql);
        if ($params === NULL) $params = array();
        if (stripos($sql, 'now()')) {
            $c = 0;
            $sql = str_ireplace('NOW()', '\''.date('Y-m-d H:i').':00\'', $sql, $c);
        }
        if ($this->isModificationQuery($sql)) $this->processModificationQuery($sql);
        else{
            if (!stripos($_SERVER['REQUEST_URI'], '/admin')) {
                $cachedFetch = $this->getCachedSelectFetch($sql);
                if ($cachedFetch != null) {
                    $cachedFetch = unserialize((string)serialize($cachedFetch));
                    return $cachedFetch;
                }
            } else return $db->queryNonCache($sql);
        }
        $tmp = $db->queryNonCache($sql);
        if (!$this->isModificationQuery($sql)) {
             $this->addSelectFetchToCache($sql, $tmp);
        }
        $freshDbFetch = unserialize((string)serialize($tmp));
        return $freshDbFetch;
    }
}
$tmp_obj = new TntdbCache();
$GLOBALS['instance'] = $tmp_obj;