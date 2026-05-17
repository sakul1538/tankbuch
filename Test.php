<?php


use PHPUnit\Framework\TestCase;

require_once 'sql.php';
class Test extends TestCase
{

    public function testDatabaseConnection()
    {
      get_kmStand();
      $this->assertTrue(true);
    }

}


