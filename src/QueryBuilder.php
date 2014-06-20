<?php

/**
 * PHP query builder 
 */
class QueryBuilder {

    protected $idField = 'id';

    protected $sqlString = null;
    
    private $identifierEscapeCharacter = '`';
    
    private $pdo;
    
    private $result = null;
    
    function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Simple select query to simplify the most common database operation.d
     * @param string $table
     * @param mixed $where @see AbstractQueryBuilder
     * @param mixed $order @see AbstractQueryBuilder
     * @param mixed $fields @see AbstractQueryBuilder
     * @return AbstractQueryBuilder 
     */
    function get($table, $where = array(), $order = array(), $fields = array()){
	$this->sqlString = $this->selectClause($fields)." from {$this->quoteIdentifier($table)} ". 
	$this->whereClause($where).' '.$this->orderByClause($order);
        $this->state = 'ready';
	return $this;
    }
    
    function insert($table, $fields){
	$clause = '('.$this->parseList(array_keys($fields), 'quoteIdentifier').') values ('.$this->parseList($fields,'quote').')';
	$this->sqlString = "insert into {$this->quoteIdentifier($table)} $clause";
        $this->state = 'ready';
	return $this;
    }

    function update($table, $fields, $where){
        $this->sqlString = "update {$this->quoteIdentifier($table)} set ".
                "{$this->parseIdValList($fields)} ".$this->whereClause($where);
        $this->state = 'ready';
	return $this;
    }
    
    function delete($table, $where){
        $this->sqlString = "delete from {$this->quoteIdentifier($table)} ".$this->whereClause($where); 
        $this->state = 'ready';
	return $this;
    }
    
    function select($fields){
        
    }
    
    function fromTable($table){
        
    }
    
    function from($tables){
        
    }
    
    function join($table, $condition){
        
    }
    
    function where($conditions){
        
    }
    
    function in($field, $list){
        
    }
    
    function orderBy($fields){
        
    }
    
    function groupBy($fields, $having){
        
    }
    
    function sql(){
        return $this->sqlString;
    }
    
    function parse($params){
        throw new Exception('Not yet implemented'.  var_export($params, true));
    }
    
    function prependSQL($sql){
        
    }
    
    function appendSQL($sql){
        
    }
    
    private function parseIdValList($list, $defaultOperator = '='){
	if (is_array($list)){
	    $clauses = array();
	    foreach($list as $id => $value){
		if (is_string($id)){
		    $clauses[$id] = "{$this->quoteIdentifier($id)} $defaultOperator {$this->pdo->quote($value)}";
		} elseif (is_int) {
		    $clauses[$id] = $value; 
		} elseif (is_array($value)) {
		    $clauses[$id] = $this->quoteIdentifier($id).' '.$value['operator'].' '.$this->quote($value['value']);
		} else {
		    throw new Exception('The list has an invalid element: '.var_export($params, true));
		}
	    }
	    return implode(', ',$clauses);
	} elseif (is_int($list)) {
	    return "{$this->quote($this->idField)} $defaultOperator {$this->quote($list)}";
	} else {
	    return $list;
	}
    }
    
    private function parseList($list, $quoteFunction) {
	if (is_array($list)){
	    return implode(', ',array_map(array($this,$quoteFunction),$list));
	} else {
	    return $list;
	}
    }
    
    private function whereClause($where){
        return empty($where) ? '' : 'where '.$this->parseIdValList($where);   
    }
    
    private function selectClause($fields){
        return empty($fields) ? 'select *' : 'select '. $this->parseList($fields,'quoteIdentifier');
    }
    
    private function orderByClause($order){
        return empty($order) ? '' : 'order by '.$this->parseOrderList($order);
    }
    
    public function quote($param) {
        return $this->pdo->quote($param);
    }
    
    public function quoteIdentifier($param) {
        $sc = $this->identifierEscapeCharacter;
        return $sc.str_replace($sc, $sc.$sc, $param).$sc;
    }
    
    function fetchAll($fetch_style = null){
        if (is_null($fetch_style)){
            $fetch = $this->pdo->query($this->sql())->fetchAll();
        } else {
            $fetch = $this->pdo->query($this->sql())->fetchAll($fetch_style);
        }
        return $fetch;
    }
    
    function fetch($fetch_style = null){
        if ($this->result === null){
            $this->result = $this->pdo->query($this->sql());
        }
        if (is_null($fetch_style)){
            $fetch = $this->pdo->query($this->sql())->fetch();
        } else {
            $fetch = $this->pdo->query($this->sql())->fetch($fetch_style);
        }
        return $fetch;
    }
    
    function exec(){
        return $this->pdo->exec($this->sql());
    }
    
    function lastInsertId(){
        return $this->pdo->lastInsertId();
    }
}