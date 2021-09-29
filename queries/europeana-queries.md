# Query alba in Europeana

Taken from https://www.wikidata.org/wiki/Wikidata:WikiProject_Alba_amicorum_National_Library_of_the_Netherlands/Source_data/Europeana

## Alba sets in Europeana
There are two sets of KB alba available on Europeana
* **EuropeanaTravel set**: https://www.europeana.eu/en/search?query=europeana_collectionName%3A%2892065_Ag_EU_TEL_a0445_ETravel%29 
* **Rise of Literacy set**:  https://www.europeana.eu/en/search?query=europeana_collectionName%3A%2816_RoL_KB_AlbaAmicorum%29 (same alba as on http://data.bibliotheken.nl/doc/dataset/rise-alba)

### Obtaining album contributions from EuropeanaTravel set via Europeana SPARQL service

* There are [28.688 objects](http://sparql.europeana.eu/?default-graph-uri=http%3A%2F%2Fdata.europeana.eu%2F&query=PREFIX+dc%3A%3Chttp%3A%2F%2Fpurl.org%2Fdc%2Felements%2F1.1%2F%3E%0D%0APREFIX+edm%3A%3Chttp%3A%2F%2Fwww.europeana.eu%2Fschemas%2Fedm%2F%3E%0D%0APREFIX+ore%3A%3Chttp%3A%2F%2Fwww.openarchives.org%2Fore%2Fterms%2F%3E%0D%0APREFIX+dcterms%3A%3Chttp%3A%2F%2Fpurl.org%2Fdc%2Fterms%2F%3E%0D%0A%0D%0ASELECT++COUNT%28%3FcollectionName%29+as+%3Fcount+%0D%0A+WHERE+%7B%0D%0A%0D%0A++++++%0D%0A+++%3Fa+edm%3AcollectionName+%3FcollectionName.%0D%0A+++FILTER+%28%3FcollectionName+%3D+%2292065_Ag_EU_TEL_a0445_ETravel%22%29%0D%0A%0D%0A%7D%0D%0A+++%0D%0A+++++++++%0D%0A%0D%0A&format=text%2Fhtml&timeout=0&debug=on ) in EuropeanaTravel set
* The Europeana SPARQL endpoint gives back max. 10.000 results per request. To see all 28.688 results, we use LIMIT & OFFSET and this trick to escape the 10.000 results limit: http://vos.openlinksw.com/owiki/wiki/VOS/VirtTipsAndTricksHowToHandleBandwidthLimitExceed
* Copy-paste the query below into http://sparql.europeana.eu/ to request ET-abla contributions
 
```
 PREFIX dc:<http://purl.org/dc/elements/1.1/>
 PREFIX edm:<http://www.europeana.eu/schemas/edm/>
 PREFIX ore:<http://www.openarchives.org/ore/terms/>
 PREFIX dcterms:<http://purl.org/dc/terms/>
 #http://vos.openlinksw.com/owiki/wiki/VOS/VirtTipsAndTricksHowToHandleBandwidthLimitExceed
 SELECT  ?a ?b ?d ?c ?GUIurl ?ETid ?catalogusurl ?ppn ?signatuur ?titel ?onderdeelVan ?folio ?afbeeldingen ?vervaardiger ?datum ?bijdrager 
 ?omvang ?taal ?soort ?wordtVermeldIn ?aggregator ?instelling ?rechtenstatus
 WHERE {{
 SELECT DISTINCT 
 ?a ?b ?c ?d 
 (GROUP_CONCAT(DISTINCT(?date);SEPARATOR = "*****") as ?datum) 
 (GROUP_CONCAT(DISTINCT(?type);SEPARATOR = "*****") as ?soort)
 (GROUP_CONCAT(DISTINCT(?title);SEPARATOR = "*****") as ?titel) 
 (GROUP_CONCAT(DISTINCT(?creator);SEPARATOR = "*****") as ?vervaardiger)
 (GROUP_CONCAT(DISTINCT(?identifier);SEPARATOR = "*****") as ?ETid) 
 (GROUP_CONCAT(DISTINCT(?ppn_temp);SEPARATOR = "*****") as ?ppn)
 (GROUP_CONCAT(DISTINCT(?kbcatresolverurl);SEPARATOR = "*****") as ?catalogusurl) 
 (GROUP_CONCAT(DISTINCT(?language);SEPARATOR = "*****") as ?taal)
 (GROUP_CONCAT(DISTINCT(?contributor);SEPARATOR = "*****") as ?bijdrager)
 (GROUP_CONCAT(DISTINCT(?partof);SEPARATOR = "*****") as ?onderdeelVan)
 (GROUP_CONCAT(DISTINCT(?image);SEPARATOR = "*****") as ?afbeeldingen)
 (GROUP_CONCAT(DISTINCT(?sig);SEPARATOR = "*****") as ?signatuur)
 (GROUP_CONCAT(DISTINCT(?fol);SEPARATOR = "*****") as ?folio)
 (GROUP_CONCAT(DISTINCT(?extent);SEPARATOR = "*****") as ?omvang)
 (GROUP_CONCAT(DISTINCT(?referencedBy);SEPARATOR = "*****") as ?wordtVermeldIn)
 (GROUP_CONCAT(DISTINCT(?copyright);SEPARATOR = "*****") as ?rechtenstatus)
 (GROUP_CONCAT(DISTINCT(?provider);SEPARATOR = "*****") as ?aggregator)
 (GROUP_CONCAT(DISTINCT(?dataprovider);SEPARATOR = "*****") as ?instelling)
 (GROUP_CONCAT(DISTINCT(?url);SEPARATOR = "*****") as ?GUIurl)
 WHERE {
 ?a edm:collectionName ?collectionName.
 FILTER (?collectionName = "92065_Ag_EU_TEL_a0445_ETravel")
 ################################################
 bind(iri(replace(substr(str(?a),1),"/aggregation/europeana/92065/","/aggregation/provider/92065/")) AS ?b)
 OPTIONAL{?b edm:isShownAt ?kbcaturl.}
   bind(STRAFTER(STR(?kbcaturl),"?PPN=") AS ?ppn2)    
   bind(iri(CONCAT("https://resolver.kb.nl/resolve?urn=PPN:",?ppn2)) AS ?kbcatresolverurl)
 OPTIONAL{?b edm:rights ?copyright.}
 OPTIONAL{?b edm:provider ?provider.}
 OPTIONAL{?b edm:dataProvider ?dataprovider.}
 ################################################
 OPTIONAL{?a edm:aggregatedCHO ?c.}
 bind(iri(replace(substr(str(?c),1),"http://data.europeana.eu/item/","https://www.europeana.eu/nl/item/")) AS ?url).
 bind(iri(replace(substr(str(?c),1),"/item/92065/","/proxy/provider/92065/")) AS ?d).
 ################################################
 OPTIONAL{?d dc:type ?type.}
 OPTIONAL{?d dc:creator ?creator.}
 OPTIONAL{?d dc:title ?title.}
 OPTIONAL{?d dc:date ?date.}
 OPTIONAL{?d dc:identifier ?identifier.
    bind(STRAFTER(?identifier,"EUROPEANATRAVEL:PPN:") AS ?ppn_temp)}
 OPTIONAL{?d dc:language ?language.}
 OPTIONAL{ ?d dc:contributor ?contributor.}
 OPTIONAL{?d dcterms:isPartOf ?partof.
    FILTER (STR(?partof) != "http://data.theeuropeanlibrary.org/Collection/a0445" && STR(?partof) != "Algemene Catalogus KB")}
 OPTIONAL{?d dcterms:hasFormat ?image. #http://resolver.kb.nl/resolve?urn=EuropeanaTravel:135F25_5b:009r
       bind(STRAFTER(STR(?image),"http://resolver.kb.nl/resolve?urn=EuropeanaTravel:") AS ?sig_fol) #135F25_5b:009r
       bind(STRBEFORE(?sig_fol,":") AS ?sig) #135F25_5b
       bind(STRAFTER(?sig_fol,":") AS ?fol) #009r
  }
 OPTIONAL{?d dcterms:extent ?extent.}
 OPTIONAL{?d dcterms:isReferencedBy ?referencedBy.}
 } GROUP BY ?a ?b ?c ?d 
 ORDER BY ASC(?a)
 }}
 #OFFSET 20000 LIMIT 10000
```

## Explore Europeana data structure in SPARQL API
*https://query.wikidata.org/#SELECT%20DISTINCT%20%3Fa%20%3Fb%20WHERE%20%7B%0A%0A%20%20SERVICE%20%3Chttp%3A%2F%2Fsparql.europeana.eu%2F%3E%20%7B%0A%20%20%20%20%20%20%20%0A%20%20%20%09%23%3Chttp%3A%2F%2Fdata.europeana.eu%2Faggregation%2Fprovider%2F92065%2FBibliographicResource_1000056096144%3E%20%3Fa%20%3Fb.%0A%20%20%20%20%23%3Chttp%3A%2F%2Fdata.europeana.eu%2Faggregation%2Feuropeana%2F92065%2FBibliographicResource_1000056096203%3E%20%3Fa%20%3Fb.%0A%20%20%20%20%23%3Chttp%3A%2F%2Fdata.europeana.eu%2Fproxy%2Fprovider%2F92065%2FBibliographicResource_1000056096203%3E%20%3Fa%20%3Fb.%0A%20%20%20%20%3Chttp%3A%2F%2Fdata.europeana.eu%2Fproxy%2Feuropeana%2F92065%2FBibliographicResource_1000056096203%3E%20%3Fa%20%3Fb.%0A%20%20%20%20%23%3Chttp%3A%2F%2Fdata.europeana.eu%2Fitem%2F92065%2FBibliographicResource_1000056096144%3E%20%3Fa%20%3Fb.%0A%20%20%20%20%23%3Chttp%3A%2F%2Fwww.europeana.eu%2Fportal%2Frecordhttp%3A%2F%2Fdata.theeuropeanlibrary.org%2FBibliographicResource%2F1000056096203%3E%20%20%3Fa%20%3Fb.%0A%20%20%0A%20%20%7D%0A%20%20%0A%20%20%20%20%7D%20LIMIT%201000%0A
