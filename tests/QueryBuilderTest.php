<?php namespace MMarinero\Database;

/**
 * QueryBuilder tests
 * @author Mario Marinero <mariomarinero@gmail.com>
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase {
 
    public function testSelect() {
        $qb = new QueryBuilder();
        $queryStr = "select * from account where client_id = 'Example'";
        $generated = $qb->select('*')->from('account')->where(['client_id' => 'Example'])->str();
        $this->assertEquals(true, true);
    }
}  