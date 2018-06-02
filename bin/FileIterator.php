<?php

/**
 * Created by PhpStorm.
 * User: deliang
 * Date: 5/30/18
 * Time: 8:46 PM
 */
class FileIterator implements iterator

{
    private $fp;
    private $index = 0;
    private $line;

    public function __construct($name)
    {
        $fp = fopen($name, "r");
        if (!$fp) {
            die("Cannot open $name for reading");
        }
        $this->fp = $fp;
        $this->line = rtrim(fgets($this->fp), "\n");
    }

    public function rewind()
    {
        $this->index = 0;
        rewind($this->fp);
        $this->line = rtrim(fgets($this->fp), "\n");
    }

    public function current()
    {
        return ($this->line);
    }

    public function key()
    {
        return ($this->index);
    }

    public function next()
    {
        $this->index++;
        $this->line = rtrim(fgets($this->fp), "\n");
        if (!feof($this->fp)) {
            return ($this->line);
        } else {
            return (NULL);
        }
    }

    public function valid()
    {
        return (feof($this->fp) ? FALSE : TRUE);
    }

}
