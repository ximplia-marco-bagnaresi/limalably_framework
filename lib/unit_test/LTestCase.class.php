<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LUnitTestException extends \Exception {
    
}

class LTestCase extends \LAssert {

    private static $total_test_cases = 0;
    private static $total_test_methods = 0;
    private static $total_test_errors = 0;
    private static $total_failures = 0;
    
    private static $failures_and_exceptions = [];

    public static function getCollectedFailuresAndExceptions() {
        return self::$failures_and_exceptions;
    }
    
    public static function getTestCaseCount() {
        return self::$total_test_cases;
    }

    public static function getTestMethodsCount() {
        return self::$total_test_methods;
    }

    public static function getTestErrorsCount() {
        return self::$total_test_errors;
    }

    public static function getFailuresCount() {
        return self::$total_failures;
    }

    public function setUp() {
        //empty
    }

    public function tearDown() {
        //empty
    }

    public static function defineRequiredClass() {
        return null;
    }

    private function callTestMethod($method_name) {
        self::$total_test_methods++;
        try {
            $this->setUp();
        } catch (\Exception $ex) {
            self::$total_test_errors++;
            LResult::message('X');
            self::$failures_and_exceptions[] = new LUnitTestException("Exception during setUp in test class ".static::class,0,$ex);
        }
        try {
            //echo "Eseguo metodo ".$method_name."\n";
            $this->{$method_name}();
        } catch (\Exception $ex) {

            if ($this->expectedException!=null) {
                if (get_class($ex)==$this->expectedException) {
                    //all is ok
                    $this->expectedException = null;
                } else {
                    self::$total_failures++;

                    LResult::message('F');
                    self::$failures_and_exceptions[] = new LUnitTestException("Raised exception does not match with expected. Found : ".get_class($ex)." - Expected : ".$this->expectedException);
                }
            } else {

                self::$total_failures++;
                
                if (!($ex instanceof LTestFailure)) {
                    LResult::message('E');
                    
                } else {
                    LResult::message('F');
                }
                self::$failures_and_exceptions[] = $ex;
            }
        }
        try {
            $this->tearDown();
        } catch (\Exception $ex) {
            self::$total_test_errors++;
            LResult::message('X');
            self::$failures_and_exceptions[] = new LUnitTestException("Exception during tearDown in test class ".static::class,0,$ex);
        }
    }

    public static function run() {
        //echo "Running test ...\n";

        if (static::defineRequiredClass()!=null && !class_exists(static::defineRequiredClass())) return;

        self::$total_test_cases++;
        $clazz_name = static::class;
        $all_methods = get_class_methods($clazz_name);
        
        foreach ($all_methods as $test_method) {
            if (strpos($test_method, 'test') === 0) {
                $class_instance = new $clazz_name();
                $class_instance->callTestMethod($test_method);
            }
        }
    }

}
