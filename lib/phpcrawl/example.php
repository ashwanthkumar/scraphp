<?php

// It may take a whils to crawl a site ...
// set_time_limit(10000);

// Inculde the phpcrawl-mainclass
include("classes/phpcrawler.class.php");

// Extend the class and override the handlePageData()-method
class MyCrawler extends PHPCrawler 
{
  function handlePageData(&$page_data) 
  {
    // Here comes your code.
    // Do whatever you want with the information given in the
    // array $page_data about a page or file that the crawler actually found.
    // See a complete list of elements the array will contain in the 
    // class-refenence.
    // This is just a simple example.
    
    // Print the URL of the actual requested page or file
    echo "Page requested: ".$page_data["url"]."<br>";
    
    // Print the first line of the header the server sent (HTTP-status)
    echo "Status: ".strtok($page_data["header"], "\n")."<br>";
    
    // Print the referer
    echo "Referer-page: ".$page_data["referer_url"]."<br>";
    
    // Print if the content was be recieved or not
    if ($page_data["received"]==true)
      echo "Content received: ".$page_data["bytes_received"]." bytes";
    else
      echo "Content not received";
    
    // ...
    
    // Now you should do something with the content of the actual
    // received page or file ($page_data[source]), we skip it in this example
    
    echo "<br><br>";
    flush();
  }
}

// Now, create an instance of the class, set the behaviour
// of the crawler (see class-reference for more methods)
// and start the crawling-process.

$crawler = &new MyCrawler();

// URL to crawl
$crawler->setURL("www.php.net");

// Only receive content of files with content-type "text/html"
// (regular expression, preg)
$crawler->addReceiveContentType("/text\/html/");

// Ignore links to pictures, dont even request pictures
// (preg_match)
$crawler->addNonFollowMatch("/.(jpg|gif|png)$/ i");

// Store and send cookie-data like a browser does
$crawler->setCookieHandling(true);

// Set the traffic-limit to 1 MB (in bytes,
// for testing we dont want to "suck" the whole site)
$crawler->setTrafficLimit(1000 * 1024);

// Thats enough, now here we go
$crawler->go();


// At the end, after the process is finished, we print a short
// report (see method getReport() for more information)

$report = $crawler->getReport();

echo "Summary:<br>";
if ($report["traffic_limit_reached"]==true)
  echo "Traffic-limit reached <br>";
  
echo "Links followed: ".$report["links_followed"]."<br>";
echo "Files received: ".$report["files_received"]."<br>";
echo "Bytes received: ".$report["bytes_received"]."<br>";

