<?php

/**
 * Displays a progress bar spanning the entire shell.
 *
 * Basic format:
 *
 *   ^MSG  PER% [=======================            ]  00:00 / 00:00$
 */
class CFTP_CLI_Progress_Bar extends \cli\Progress {
    protected $_bars = '=>';
    protected $_formatMessage = '{:msg}  {:percent}% [';
    protected $_formatTiming = '] {:elapsed} / {:estimated}';
    protected $_format = '{:msg}{:bar}{:timing}';


    protected $cols = false;

    /**
     * Prints the progress bar to the screen with percent complete, elapsed time
     * and estimated total time.
     *
     * @param boolean  $finish  `true` if this was called from
     *                          `cli\Notify::finish()`, `false` otherwise.
     * @see cli\out()
     * @see cli\Notify::formatTime()
     * @see cli\Notify::elapsed()
     * @see cli\Progress::estimated();
     * @see cli\Progress::percent()
     */
    public function display($finish = false) {
        $_percent = $this->percent();

        $percent = str_pad(floor($_percent * 100), 3);;
        $msg = $this->_message;
        $msg = \cli\render($this->_formatMessage, compact('msg', 'percent'));

        $estimated = $this->formatTime($this->estimated());
        $elapsed   = str_pad($this->formatTime($this->elapsed()), strlen($estimated));
        $timing    = \cli\render($this->_formatTiming, compact('elapsed', 'estimated'));

        $size = $this->columns();
        $size = $size >= strlen($msg . $timing) ? $size-strlen($msg . $timing) : 0;

        $bar = str_repeat($this->_bars[0], floor($_percent * $size)) . $this->_bars[1];
        // substr is needed to trim off the bar cap at 100%
        $bar = substr(str_pad($bar, $size, ' '), 0, $size);

        \cli\out($this->_format, compact('msg', 'bar', 'timing'));
    }

    public function columns() {
        if ( is_numeric( $this->cols ) ) {
            return $this->cols;
        }
        if ( stripos( PHP_OS, 'indows' ) === false ) {
            $this->cols = exec('tput cols');
            if ( is_numeric( $this->cols ) ) {
                return $this->cols;
            }
        }
        $this->cols = 80; // default width of cmd window on Windows OS, maybe force using MODE CON COLS=XXX?
        return $this->cols;

    }
}





