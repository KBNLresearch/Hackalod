<?php
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
                    'User-Agent: WDQS-example PHP/' . PHP_VERSION, // TODO adjust this; see https://w.wiki/CX6
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
$sparqlQueryString = <<< 'SPARQL'
#defaultView:ImageGrid{"hide":["?gender","?portrait"]}
SELECT DISTINCT ?contributorDescription ?contributor ?contributorLabel ?gender ?portrait WHERE { 
  BIND(wd:Q72752496 as ?album)
 
  ?album wdt:P767 ?contributor.
  ?contributor wdt:P21 ?gender.
  OPTIONAL{?contributor wdt:P18 ?image.}

  BIND (wd:Q82985930 as ?maledummy) 
  BIND (wd:Q82992173 as ?femaledummy)  
  ?maledummy wdt:P18 ?maledummyimage.
  ?femaledummy wdt:P18 ?femaledummyimage.
  BIND(IF(?gender=wd:Q6581072,?femaledummyimage,?maledummyimage) as ?dummyimage). #Choose the dummyimage dependent on gender (female/male)
   
  BIND(IF(BOUND(?image), ?image,?dummyimage) as ?portrait). #If no image is known, substitute the dummy image
   
  SERVICE wikibase:label { bd:serviceParam wikibase:language "en,nl". }
} 
ORDER BY ?contributorLabel
SPARQL;

$queryDispatcher = new SPARQLQueryDispatcher($endpointUrl);
$queryResult = $queryDispatcher->query($sparqlQueryString);

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
                 <input type="text" placeholder="Soecken op Forum Amicorum" name="search" class = 'search'>
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
        <img src="images/Heyblocq/background_image.jpg" alt="logo" class = "background_img"> 
            <div class = 'profile_div'>
                <img src="images/Heyblocq/profile_img.jpg" alt="logo" class = "profile_img"> 
            </div>
        </div>
        <div class = 'header_bio'>
            Jacob Heyblocq
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
                    <p class = 'p_intro'> dichter en rector</p>
                    <img src="public/img/place.jpg" alt="logo" class = "intro_img"> 
                    <p class = 'p_intro'>Lives in <b>Amsterdam</b></p>
                    <img src="public/img/from.jpg" alt="logo" class = "intro_img"> 
                    <p class = 'p_intro'>From <b>Amsterdam</b></p>
                </div>
                <div class = 'body_content'>
                   <h1>Photos</h1>
                   <div class = 'photos'>
                   <img src="images/Heyblocq/gallery_img.jpg" alt="logo" class = "photo_img"> 
                </div>
                </div>
                <div class = 'body_content'>
                   <h1>Friends</h1>
<?php
                   $contributor_info = array_column($queryResult, 'bindings');
                   $count = 0;
                    foreach($contributor_info as $result) {
                        foreach($result as $res){
                            $uri = $res['contributor']['value'];
                            $uri_decomp = explode("/", $uri);
                            $identifier = end($uri_decomp);
                            if ($identifier == 'Q2039960') {
                                // Dit is Jacobus Heiblocq zelf, dus overslaan
                            }
                            else {
                                $friendname = $res['contributorLabel']['value']; 
                                $output[] = '<div class = "friend">
                                    <a href = "profiel.php?id='.$identifier.'"><img src="images/friends/'.$identifier.'.jpg" alt="logo" class = "friends_img"> </br></a>
                                    '.$friendname.'</div>';
                                $count += 1;
                            }
                        }
   
                    }
                    echo '<div class = "friends">
                    <div>'.$count.' friends</div>';
                    echo implode('', $output);
?>          
                
                    </div>
                </div>
            </div>
            <div class = 'body_right'>
                <div class = 'body_content'>
                    <h1>Posts</h1>
                </div>
                <div class = 'body_content post'>
                    <img src="gallery_temp.jpg" alt="logo" class = "post_prof"> 
                    <p class = 'p_title'>
                    <b>Jacob Heyblocq</b></br>
                    1645 -
                    <img src="public/img/vooriedereen.jpg" alt="logo" class = "post_public"> 
                    </p>
                    <p class = 'p_text'>
                    Begonnen aan een alba amicorum, geef een like als je ook een bijdrage wilt leveren!
                    #friends4life #vriendenvoorhetleven #alba amicorum</p>
                    <img src="images/hey11.jpg" alt="logo" class = "post_img"> 
                    <img src="<?php echo $like;?>" alt="logo" class = "likes">   
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
    </body>

</html>

