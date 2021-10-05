<?php
$id = $_GET['id'];
$test = "http://www.wikidata.org/entity/Q381801";

////////////////////////////////////////////////////// SPARQL Query ////////////////////////////////////////////////////////////////////////////
class SPARQLQueryDispatcher
{
    private $endpointUrl;

    public function __construct(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    public function query(string $sparqlQuery): array
    {

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:92.0) Gecko/20100101 Firefox/92.0' // TODO adjust this; see https://w.wiki/CX6
                ],
            ],
        ];
        $context = stream_context_create($opts);

        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}

$endpointUrl = 'https://query.wikidata.org/sparql';
$sparqlQueryString = '
#defaultView:ImageGrid{"hide":["?gender","?portrait"]}
SELECT DISTINCT ?contributorDescription ?contributor ?contributorLabel ?gender ?portrait WHERE { 
  BIND(wd:Q72752496 as ?album)
 
  FILTER (?contributor = wd:'.$id.')
  
  ?album wdt:P767 ?contributor.
  ?contributor wdt:P21 ?gender.
  OPTIONAL{?contributor wdt:P18 ?image.}

  BIND (wd:Q82985930 as ?maledummy) 
  BIND (wd:Q82992173 as ?femaledummy)  
  ?maledummy wdt:P18 ?maledummyimage.
  ?femaledummy wdt:P18 ?femaledummyimage.
  BIND(IF(?gender=wd:Q6581072,?femaledummyimage,?maledummyimage) as ?dummyimage). #Choose the dummyimage dependent on gender (female/male)
   
  BIND(IF(BOUND(?image), ?image,?dummyimage) as ?portrait). #If no image is known, substitute the dummy image
   
  SERVICE wikibase:label { bd:serviceParam wikibase:language "nl". }
} 
ORDER BY ?contributorLabel';

$queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
$queryResult = $queryDispatcher->query($sparqlQueryString);


class SPARQLQueryDispatcher_art
{
    private $endpointUrl;

    public function __construct(string $endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
    }

    public function query(string $sparqlQuery): array
    {

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Accept: application/sparql-results+json',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:92.0) Gecko/20100101 Firefox/92.0' // TODO adjust this; see https://w.wiki/CX6
                ],
            ],
        ];
        $context = stream_context_create($opts);

        $url = $this->endpointUrl . '?query=' . urlencode($sparqlQuery);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}

$endpointUrl = 'https://query.wikidata.org/sparql';
$sparqlQueryString_art = '
SELECT ?contr ?contrLabel  
       ?item ?itemLabel ?itemDescription ?image ?gemaakt ?collectieLabel WHERE {
 
   wd:Q72752496 wdt:P767 ?contr.

   FILTER (?contr = wd:'.$id.')

    OPTIONAL { ?item  wdt:P170 ?contr ;
                      wdt:P31/wdt:P279*  wd:Q838948 ; # Behoort tot artwork (subclass)
                      wdt:P571 ?gemaakt ;
                      wdt:P195 ?collectie .

                  OPTIONAL { ?item wdt:P18 ?image } # Optionally with an image

             
             }

         SERVICE wikibase:label { bd:serviceParam wikibase:language "nl". }
       }
ORDER BY ?contrLabel ?gemaakt
LIMIT 5';

$queryDispatcher_art = new SPARQLQueryDispatcher($endpointUrl);
$queryResult_art = $queryDispatcher_art->query($sparqlQueryString_art);
#echo $sparqlQueryString;
#echo "<br>";
#var_export($queryResult_art);



////////////////////////////////////////////////////// Image rezise function ////////////////////////////////////////////////////////////////////////////



function rezise($filename, $width, $height, $type){
    // The file
	//echo $filename;

    if ($type == 'profile'){
        $dest_file = 'profile_temp.jpg';
    }
    else if ($type == 'gallery'){
        $dest_file = 'gallery_temp.jpg';
    }
    else if ($type == 'photo'){
        $dest_file = 'photo_temp.jpg';
    }
    else if ($type == 'post'){
        $dest_file = 'post_temp.jpg';
    }
    else {
        $dest_file = 'temp.jpg';
    }
    // Get new dimensions
    list($width_orig, $height_orig) = getimagesize($filename);

    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
        $height = $width/$ratio_orig;
   
    } else {
        $width = $height*$ratio_orig;
    }

    // Resample
    $image_p = imagecreatetruecolor($width, $height);
    $image = imagecreatefromjpeg($filename);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    // Output
    imagejpeg($image_p, $dest_file);

    $im = imagecreatefromjpeg($dest_file);
    $size = min(imagesx($im), imagesy($im));
    list($width_orig, $height_orig) = getimagesize($dest_file);

    $im2 = imagecrop($im, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
    if ($im2 !== FALSE) {
        imagejpeg($im2, $dest_file);
        imagedestroy($im2);
    }
    imagedestroy($im);
}




$filename_dbnl = 'temp.txt';
$content = file_get_contents("https://www.dbnl.org/nieuws/text.php?id=vond001hier01");
file_put_contents($filename_dbnl, $content);

////////////////////////////////////////////////////// SPAQL results for page ////////////////////////////////////////////////////////////////////////////



$contributor_info = array_column($queryResult, 'bindings');
foreach($contributor_info as $result) {
    foreach($result as $res){
        //print_r($res);
        $uri = $res['contributor']['value'];
        $uri_decomp = explode("/", $uri);
        $identifier = end($uri_decomp);
        if ($id == $identifier){
            $name = $res['contributorLabel']['value']; 
            $occupation = $res['contributorDescription']['value']; 
            $occupation = explode("(", $occupation);
            $occupation = $occupation[0];
            $portrait =$res['portrait']['value'];  
			$output = 'temp.jpg';
			ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)'); 
			file_put_contents($output, file_get_contents($portrait));
            //echo $portrait;
			//echo $occupation;
          } 
    }
}

$art = array_column($queryResult_art, 'bindings');
//print_r($art);

foreach($art as $result) {
            foreach($result as $res){
				//echo "Art";
       
        //print_r($res);
        $uri = $res['contr']['value'];
        $uri_decomp = explode("/", $uri);
        $identifier = end($uri_decomp);
        if ($id == $identifier){
            if (!empty($res['image']['value'])){
            $image = $res['image']['value'];
			##echo $image;
            break;
            }
        }
    }
}



$post_image = rezise($output, 154, 154, 'profile');
$profile_resize = rezise($output, 154, 154, 'profile');
$gallery_resize = rezise($output, 107, 107, 'gallery');

$profile_pic = 'profile_temp.jpg';
$gallery_pic = 'gallery_temp.jpg';
$background_pic = 'images/background_painting.jpg';

$random = rand(0, 9);

$handle = fopen("data/lines.csv", "r");
while (($data = fgetcsv($handle)) !== FALSE) {
    $line = $data[0];
    if ($line == $random){
        $sentence = $data[1];
    }
}

$randomlike = rand(1, 3);
$like = 'public/img/likes'.$randomlike.'.jpg';
//echo $like;
?>



<html>
    <head>
    <link rel = "stylesheet" type = "text/css" href = "public/css/default.css" />
    </head>
    <body>
    <div class = 'menu'>
        <div class = 'logo_div'>
            <img src="public/img/logo.jpg" alt="logo" class = "logo"> 
        </div>
        <div class="search-container">
                <img src="public/img/magnifier.jpg" alt="logo" class = "mag"> 
                 <input type="text" placeholder="Search Facebook" name="search" class = 'search'>
        </div>
        <div class = 'buttons'>
            <div class ='buttons_img'>
                <img src="public/img/home.jpg" alt="logo" class = "icon_menu"> 
            </div>
            <div class ='buttons_img'>
                <img src="public/img/friends.jpg" alt="logo" class = "icon_menu"> 
            </div>
                <div class ='buttons_img'>
            <img src="public/img/video.jpg" alt="logo" class = "icon_menu"> 
            </div>
            <div class ='buttons_img'>
                <img src="public/img/marketplace.jpg" alt="logo" class = "icon_menu">
            </div> 
            <div class ='buttons_img'>
                <img src="public/img/world.jpg" alt="logo" class = "icon_menu"> 
            </div>
        </div>

    </div>
<div class = 'content'>
    <div class = 'page_header'>
      
        <div class = 'header_img'>
        <img src="<?php echo $background_pic?>" alt="logo" class = "background_img"> 
            <div class = 'profile_div'>
                <img src="<?php echo $profile_pic?>" alt="logo" class = "profile_img"> 
            </div>
        </div>
        <div class = 'header_bio'>
            <?php echo $name ?>
        </div>
        <div class = "line_div">
        <hr class="line">
        </div>
        <div class = 'submenu'>
            <button class = 'subbutton'>Posts</button>
            <button class = 'subbutton'>About</button>
            <button class = 'subbutton'>Friends</button>
            <button class = 'subbutton'>Photos</button>
            <button class = 'subbutton'>Videos</button>
            <button class = 'subbutton'>More</button>
        </div>
    </div>
    <div class = 'page_body'>
        <div class = 'body_container'>
            <div class = 'body_left'>
                <div class = 'body_content'>
                    <h1>Intro</h1>
                    <img src="public/img/work.jpg" alt="logo" class = "intro_img"> 
                    <p class = 'p_intro'><?php echo $occupation?></p>
                    <img src="public/img/place.jpg" alt="logo" class = "intro_img"> 
                    <p class = 'p_intro'>Lives in <b>?</b></p>
                    <img src="public/img/from.jpg" alt="logo" class = "intro_img"> 
                    <p class = 'p_intro'>From <b>?</b></p>
                </div>
                <div class = 'body_content'>
                   <h1>Photos</h1>
                   <div class = 'photos'>
                   <img src="<?php echo $gallery_pic?>" alt="logo" class = "photo_img"> 
                   <?php
                   if (!isset($image)){
                   }
                   else{
                    $post_photo = rezise($image, 107, 107, 'photo');
                       ?>
                   <img src="photo_temp.jpg" alt="logo" class = "photo_img">
                    <?php
                   }
                   ?>
                 
                </div>
                </div>
                <div class = 'body_content'>
                   <h1>Friends</h1>
                   <div class = 'friends'>
                        <div>1 friend</div>
                        <div class = 'friend'>
                            <a href = 'index.php'><img src="images/Heyblocq/gallery_img.jpg" alt="logo" class = "friends_img"> </br></a>
                            Jacob Heyblocq
                        </div>
                    </div>
                </div>
            </div>
            <div class = 'body_right'>
                <div class = 'body_content'>
                    <h1>Posts</h1>
                </div>
                <?php
                $file_data = array_slice(file('temp.txt'), 160, 10);
				if (isset($image)){
                                        
                    ?>
                <div class = 'body_content post'>
                    <img src="gallery_temp.jpg" alt="logo" class = "post_prof"> 
                    <p class = 'p_title'>
                    <b><?php echo $name; ?></b></br>
                     -
                    <img src="public/img/vooriedereen.jpg" alt="logo" class = "post_public"> 
                    </p>
                    <p class = 'p_text'>
                    <?php echo $sentence; ?></br>
                    #finished</p>
                    <img src="<?php echo $image;?>" alt="logo" class = "post_img"> 
                    <img src="<?php echo $like;?>" alt="logo" class = "likes">   
                </div>
                <?php
                }
                else if (isset($file_data)){
                    $text = "";
                    foreach ($file_data as $line) {
                        $text  = $text.$line."<br>";
                    }
        

                    ?>
                    <div class = 'body_content post'>
                    <img src="gallery_temp.jpg" alt="logo" class = "post_prof"> 
                    <p class = 'p_title'>
                    <b><?php echo $name; ?></b></br>
                     -
                    <img src="public/img/vooriedereen.jpg" alt="logo" class = "post_public"> 
                    </p>
                    <p class = 'p_text'>
                    <?php echo $text ?></br>
                    
                    
                    <img src="<?php echo $like;?>" alt="logo" class = "likes">   
                </div>
                    <?php
                 
                }
            
                
                else {

                }
                ?>
               
            </div>
        </div>
    </div>
    </div>
</div>
    </body>

</html>