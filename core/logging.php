<?php

require_once 'main.inc.php';

/**
 * Defines how a logging object should look like. This is the base for every
 * logging object. (E.g. txt files, sql DB, ...)
 */
interface ILoggingObserver {
    public function Open();

    public function Write($errorLevel, $message);

    public function Write($message);

    public function Close();
}

/**
 * Defines the possible logging error level.
 */
class ErrorLevel {
    const INFORMATION = 1;
    const WARNING = 2;
    const ERROR = 4;
}

/**
 * Contains all ILoggingObserver which are called when there is something to log.
 * Due this it's very easy to add logging classes for every filetype.
 */
class LoggingPublisher {
    protected $LoggingObserver;

    public function  __construct() {
        $this->LoggingObserver = array();
    }

    /**
     * Adds the ILoggingObserver object to the list of the logging observer.
     * After adding the object it gets called with the next write-call.
     * @param ILoggingObserver $logger 
     */
    public function AddLogger($logger) {
        if(!($logger instanceof ILoggingObserver))
            throw new WrongTypeException ("The logger object wasn't of the type ILoggingObserver!");
        
        $index = count($this->LoggingObserver);
        $this->LoggingObserver[$index] = $logger;
    }

    /**
     * Removes the given $logger object. If the $logger object isn't in the
     * list nothing happens.
     * @param ILoggingObserver $logger
     */
    public function RemoveLogger($logger) {
        $index = array_search($logger, $this->LoggingObserver, true);

        if($index)
            unset($this->LoggingObserver[$index]);
    }

    /**
     * Writes to every logger in the LoggingObserver list the message with the
     * given error level.
     * @param ErrorLevel $errorLevel
     * @param Mixed $message
     */
    public function Write($errorLevel, $message) {
        foreach($this->LoggingObserver as $logger)
            $logger->Write($errorLevel, $message);
    }
}
?>
