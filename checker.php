<?php

interface Technology {
    
    /**
     * Returns the name of Technology
     * @return String
     */ 
    function getName();
    
    /**
     * Returns true if given Website object using Technology
     * @return Boolean
     */ 
    function check(Website $website);
}

class GoogleAnalytics implements Technology {
    
    public function getSignatures() {
        return ['.google-analytics.com/ga.js', 'ga.async = true;'];
    }
    
    public function getName() {
        return "GA";
    }
    
    public function check(Website $website) {
        foreach($this->getSignatures() as $sign) {
            if (strpos($website->getContent(), $sign)) return true;
        }
        return false;
    }
}

class DynDNS implements Technology {
    
    public function getSignatures() {
        return ['dynect.net', 'dns.dyn.com'];
    }
    
    public function getName() {
        return "Dyn";
    }
    
    public function check(Website $website) {
        $url = parse_url($website->getUrl());
        $records = dns_get_record($url['host']);
        foreach($records as $record) {
            foreach($this->getSignatures() as $sign) {
                if (strpos($record['target'], $sign)) return true;
            }
        }
        return false;
    }
}

class Website {
    
    private $url;
    private $content;
    
    public function __construct($url) {
        $this->url = $this->fixUrl($url);
    }
    
    public function getUrl() {
        return $this->url;
    }
    
    public function getContent() {
        if ($this->content === null) {
            $this->content = $this->getPageContents();
        }
        return $this->content;
    }
    
    private function getPageContents() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $output = curl_exec($ch);
        if ($output === false) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch); 
        
        return $output;
    }
    
    private function fixUrl($url) {
        $url = strtolower($url);
        if (substr( $url, 0, 7 ) != "http://" && substr( $string_n, 0, 8 ) != "https://") $url = "http://".$url;
        return $url;
    }
}

class Checker {
    
    private $website;
    private $technologies = [];
    
    public function __construct(Website $website, Technology $technology) {
        $this->website = $website;
        $this->addTechnology($technology);
    }
    
    public function addTechnology(Technology $technology) {
        $this->technologies[] = $technology;
    }
    
    public function check() {
        foreach($this->technologies as $tech) {
            if ($tech->check($this->website)) {
                $this->printResult($tech->getName(), "yes");
            } else {
                $this->printResult($tech->getName(), "no");
            }
        }
    }
    
    private function printResult($name, $result) {
        echo "Using " . $name . ": ";
        echo $result . "\n";
    }
}




// Website object with URL from the input
$website = new Website($argv[1]);
// Instantiate Checker with Website and Technology
$checker = new Checker($website, new GoogleAnalytics());
// add more technologies
$checker->addTechnology(new DynDNS());
// run the check
try{
    $checker->check();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
