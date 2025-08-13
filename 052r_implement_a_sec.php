<?php

class SecureCLIToolAnalyzer {
    private $toolName;
    private $toolPath;
    private $allowedFlags = array('-h', '--help', '-v', '--version');
    private $disallowedFlags = array('-r', '--root', '-s', '--sudo');

    public function __construct($toolName, $toolPath) {
        $this->toolName = $toolName;
        $this->toolPath = $toolPath;
    }

    public function analyze() {
        $output = array();
        $command = escapeshellcmd($this->toolPath . ' ' . $this->toolName);
        $commandOutput = shell_exec($command);
        $output[] = $commandOutput;

        $flags = $this->getFlags();
        foreach ($flags as $flag) {
            if (in_array($flag, $this->allowedFlags)) {
                $output[] = "Flag $flag is allowed";
            } elseif (in_array($flag, $this->disallowedFlags)) {
                $output[] = "Flag $flag is disallowed";
            } else {
                $output[] = "Flag $flag is unknown";
            }
        }

        return $output;
    }

    private function getFlags() {
        $flags = array();
        $command = escapeshellcmd($this->toolPath . ' ' . $this->toolName . ' --help');
        $commandOutput = shell_exec($command);
        preg_match_all('/-([a-zA-Z])|--[a-zA-Z-]+/', $commandOutput, $matches);
        $flags = array_merge($flags, $matches[0]);

        return $flags;
    }
}

$analyzer = new SecureCLIToolAnalyzer('mytool', '/usr/local/bin/');
print_r($analyzer->analyze());

?>