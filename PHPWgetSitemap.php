<?php

// PHP Wget Sitemap generator v0.2
// (c) 2008 by Paolo Ardoino < paolo.ardoino@gmail.com >

class PHPWgetSitemap {
    public $opts = array("sitemap_file" => "sitemap.xml", "website_url" => "");
    public $sitemap = array();

    function __construct() {
        echo "PHP Wget Sitemap generator v0.2\t(c) 2008 by Paolo Ardoino < paolo.ardoino@gmail.com >\n";
    }

    function setSitemapFile($sitemap_file) {
        $this->opts["sitemap_file"] = $sitemap_file;
    }

    function setWebsiteUrl($website_url) {
        $this->opts["website_url"] = $website_url;
    }

    function mirror() {
        if($this->opts["website_url"] != "") {
            echo "Wget: fetching '".$this->opts["website_url"]."' website\n";
            exec("wget -m ".$this->opts["website_url"]." 2> wget.log");
        }
    }

    function generate() {
        if($this->opts["website_url"] != "") {
            $website_dir = substr($this->opts["website_url"], 7);
            if($website_dir != "") {
                echo "PHPWgetSitemap: scanning '".$website_dir."' for sitemap generation\n";
                $this->sitemap = $this->_scan($website_dir);
                $this->ssave();
            }
        }
    }

    function _scan($dir) {
        $sitemap = array();
        $FILES_EXCLUDE = array(".", "..", "index.php", "index.html", "index.htm");
        
        if($dir != "") {
            if (is_dir($dir)) {
                if ($handle = opendir($dir)) {
                    chdir($dir);
                    $sitemap[] = $dir."/";
                    while (false !== ($file = readdir($handle))) {
                        if (!in_array($file, $FILES_EXCLUDE)) {
                            if(is_dir($file)) {
                                $arr = $this->_scan($file);
                                foreach ($arr as $value) {
                                    $sitemap[] = $dir."/".$value;
                                }
                            } else {
                                $sitemap[] = $dir."/".$file;
                            }
                        }
                     } 
                    chdir("../");
                }
                closedir($handle);
            }
        }
        return $sitemap;
    }

    function ssave() {
        $sitemap_file = $this->opts["sitemap_file"];
        if($sitemap_file != "") {
            if($fp = fopen($sitemap_file, "w+")) {
                $out = '<?xml version="1.0" encoding="UTF-8"?>
                    <urlset
                    xmlns="http://www.google.com/schemas/sitemap/0.84"
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84
                    http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';
                for($i = 0, $y = sizeof($this->sitemap); $i < $y; $i++) {
                    $out .= "<url>\n\t<loc>http://".$this->sitemap[$i]."</loc>\n\t<priority>0.500</priority>\n</url>\n";
                }
                $out .= '</urlset>';
                fputs($fp, $out);
                fclose($fp);
                echo "Sitemap has been written to '".$sitemap_file."'.\n";
            } else {
                echo "Error: cannot save '".$sitemap_file."' file.\n";
            }
        }
    }
    
}

?>