# Query alba in data.bibliotheken.nl
Taken from https://www.wikidata.org/wiki/Wikidata:WikiProject_Alba_amicorum_National_Library_of_the_Netherlands/Extract-KB-LOD-AA

## Alba set in data.bibliotheken.nl
* http://data.bibliotheken.nl/doc/dataset/rise-alba - these are the same alba as [in Europeana](https://www.europeana.eu/en/search?query=europeana_collectionName%3A%2816_RoL_KB_AlbaAmicorum%29)
* SPARQL endpoint: http://data.bibliotheken.nl/sparql

## 1) Querying alba (+contributions) through data.bibliotheken.nl SPARQL endpoint
### a) Alba only (without GROUP_CONCAT)
```
 SELECT DISTINCT ?album ?inventoryNumber ?albumtitle ?image WHERE {
 ?inscription foaf:isPrimaryTopicOf/void:inDataset <http://data.bibliotheken.nl/id/dataset/rise-alba> .
 ?inscription schema:isPartOf ?album .
 ?album schema:name ?albumtitle .
 ?album schema:identifier ?inventoryNumber. 
 ?album schema:dateCreated ?dateCreated .
 ?album schema:image ?a .
 ?a schema:contentUrl ?image .
 } ORDER BY ?album
```
[Try this query](http://data.bibliotheken.nl/sparql?qtxt=SELECT+DISTINCT+%3Falbum+%3FinventoryNumber+%3Falbumtitle+%3Fimage+WHERE+{%0D%0A+%3Finscription+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Finscription+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+%3Falbum+schema%3Aidentifier+%3FinventoryNumber.+%0D%0A+%3Falbum+schema%3AdateCreated+%3FdateCreated+.%0D%0A+%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+}+ORDER+BY+%3Falbum&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on&run=+Run+Query+) -- [See query result](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=SELECT+DISTINCT+%3Falbum+%3FinventoryNumber+%3Falbumtitle+%3Fimage+WHERE+%7B%0D%0A+++%3Finscription+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+++%3Finscription+schema%3AisPartOf+%3Falbum+.%0D%0A+++%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+++%3Falbum+schema%3Aidentifier+%3FinventoryNumber.+%0D%0A+++%3Falbum+schema%3AdateCreated+%3FdateCreated+.%0D%0A+++%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+++%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+++%7D+ORDER+BY+%3Falbum+%0D%0A&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) -- [Result as JSON](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=+SELECT+DISTINCT+%3Falbum+%3FinventoryNumber+%3Falbumtitle+%3Fimage+WHERE+%7B%0D%0A+%3Finscription+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Finscription+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+%3Falbum+schema%3Aidentifier+%3FinventoryNumber.+%0D%0A+%3Falbum+schema%3AdateCreated+%3FdateCreated+.%0D%0A+%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+%7D+ORDER+BY+%3Falbum&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on&run=+Run+Query+)

### b) Alba with GROUP_CONCAT
_Retrieves 57 alba_
```
 SELECT DISTINCT 
 ?album
 ?albumtitle
 (GROUP_CONCAT(DISTINCT(?owner); separator="****") as ?albumowner)
 (GROUP_CONCAT(DISTINCT(?albumdescr); separator="****") as ?albumdescription)
 (GROUP_CONCAT(DISTINCT(?albumlocation); separator="****") as ?albumlocationcreated)
 (GROUP_CONCAT(DISTINCT(?inventory); separator="****") as ?inventoryNumber)
 (GROUP_CONCAT(DISTINCT(?material); separator="****") as ?albummaterial)
 (GROUP_CONCAT(DISTINCT(?date); separator="****") as ?dateCreated)
 ?albumpages 
 ?albumwidth
 ?albumheight
 #(GROUP_CONCAT(DISTINCT(?image); separator="****") as ?albumimages)
 WHERE {
 ?contrib foaf:isPrimaryTopicOf/void:inDataset <http://data.bibliotheken.nl/id/dataset/rise-alba> .
 ?contrib schema:isPartOf ?album .
 ?album schema:name ?albumtitle .
 OPTIONAL{?album schema:numberOfPages ?albumpages.}
 #?album schema:image ?a .
 #?a schema:contentUrl ?image .
 #CONCAT fields
 OPTIONAL{?album schema:material ?material.} 
 OPTIONAL{?album schema:description ?albumdescr.}
 OPTIONAL{?album schema:locationCreated ?albumlocation.}
 OPTIONAL{?album schema:identifier ?inventory.} 
 OPTIONAL{?album schema:width ?albumwidth.}
 OPTIONAL{?album schema:height ?albumheight.}
 OPTIONAL{?album schema:dateCreated ?date.}
 OPTIONAL{?album schema:author ?owner.} 
 } #GROUP BY ?album  ?albumwidth ?albumheight #?albumpages
 ORDER BY ?album
```
[Try this query](http://data.bibliotheken.nl/sparql?qtxt=SELECT+DISTINCT+%0D%0A+%3Falbum%0D%0A+%3Falbumtitle%0D%0A+(GROUP_CONCAT(DISTINCT(%3Fowner)%3B+separator%3D%22****%22)+as+%3Falbumowner)%0D%0A+(GROUP_CONCAT(DISTINCT(%3Falbumdescr)%3B+separator%3D%22****%22)+as+%3Falbumdescription)%0D%0A+(GROUP_CONCAT(DISTINCT(%3Falbumlocation)%3B+separator%3D%22****%22)+as+%3Falbumlocationcreated)%0D%0A+(GROUP_CONCAT(DISTINCT(%3Finventory)%3B+separator%3D%22****%22)+as+%3FinventoryNumber)%0D%0A+(GROUP_CONCAT(DISTINCT(%3Fmaterial)%3B+separator%3D%22****%22)+as+%3Falbummaterial)%0D%0A+(GROUP_CONCAT(DISTINCT(%3Fdate)%3B+separator%3D%22****%22)+as+%3FdateCreated)%0D%0A+%3Falbumpages+%0D%0A+%3Falbumwidth%0D%0A+%3Falbumheight%0D%0A+%23(GROUP_CONCAT(DISTINCT(%3Fimage)%3B+separator%3D%22****%22)+as+%3Falbumimages)%0D%0A+WHERE+{%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+OPTIONAL{%3Falbum+schema%3AnumberOfPages+%3Falbumpages.}%0D%0A+%23%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+%23%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+%23CONCAT+fields%0D%0A+OPTIONAL{%3Falbum+schema%3Amaterial+%3Fmaterial.}+%0D%0A+OPTIONAL{%3Falbum+schema%3Adescription+%3Falbumdescr.}%0D%0A+OPTIONAL{%3Falbum+schema%3AlocationCreated+%3Falbumlocation.}%0D%0A+OPTIONAL{%3Falbum+schema%3Aidentifier+%3Finventory.}+%0D%0A+OPTIONAL{%3Falbum+schema%3Awidth+%3Falbumwidth.}%0D%0A+OPTIONAL{%3Falbum+schema%3Aheight+%3Falbumheight.}%0D%0A+OPTIONAL{%3Falbum+schema%3AdateCreated+%3Fdate.}%0D%0A+OPTIONAL{%3Falbum+schema%3Aauthor+%3Fowner.}+%0D%0A+}+%23GROUP+BY+%3Falbum++%3Falbumwidth+%3Falbumheight+%23%3Falbumpages%0D%0A+ORDER+BY+%3Falbum&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) -- [See query result](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=+SELECT+DISTINCT+%0D%0A+%3Falbum%0D%0A+%3Falbumtitle%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fowner%29%3B+separator%3D%22****%22%29+as+%3Falbumowner%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Falbumdescr%29%3B+separator%3D%22****%22%29+as+%3Falbumdescription%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Falbumlocation%29%3B+separator%3D%22****%22%29+as+%3Falbumlocationcreated%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Finventory%29%3B+separator%3D%22****%22%29+as+%3FinventoryNumber%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fmaterial%29%3B+separator%3D%22****%22%29+as+%3Falbummaterial%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fdate%29%3B+separator%3D%22****%22%29+as+%3FdateCreated%29%0D%0A+%3Falbumpages+%0D%0A+%3Falbumwidth%0D%0A+%3Falbumheight%0D%0A+%23%28GROUP_CONCAT%28DISTINCT%28%3Fimage%29%3B+separator%3D%22****%22%29+as+%3Falbumimages%29%0D%0A+WHERE+%7B%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+OPTIONAL%7B%3Falbum+schema%3AnumberOfPages+%3Falbumpages.%7D%0D%0A+%23%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+%23%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+%23CONCAT+fields%0D%0A+OPTIONAL%7B%3Falbum+schema%3Amaterial+%3Fmaterial.%7D+%0D%0A+OPTIONAL%7B%3Falbum+schema%3Adescription+%3Falbumdescr.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3AlocationCreated+%3Falbumlocation.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aidentifier+%3Finventory.%7D+%0D%0A+OPTIONAL%7B%3Falbum+schema%3Awidth+%3Falbumwidth.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aheight+%3Falbumheight.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3AdateCreated+%3Fdate.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aauthor+%3Fowner.%7D+%0D%0A+%7D+%23GROUP+BY+%3Falbum++%3Falbumwidth+%3Falbumheight+%23%3Falbumpages%0D%0A+ORDER+BY+%3Falbum&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) -- [Result as JSON](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=+SELECT+DISTINCT+%0D%0A+%3Falbum%0D%0A+%3Falbumtitle%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fowner%29%3B+separator%3D%22****%22%29+as+%3Falbumowner%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Falbumdescr%29%3B+separator%3D%22****%22%29+as+%3Falbumdescription%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Falbumlocation%29%3B+separator%3D%22****%22%29+as+%3Falbumlocationcreated%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Finventory%29%3B+separator%3D%22****%22%29+as+%3FinventoryNumber%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fmaterial%29%3B+separator%3D%22****%22%29+as+%3Falbummaterial%29%0D%0A+%28GROUP_CONCAT%28DISTINCT%28%3Fdate%29%3B+separator%3D%22****%22%29+as+%3FdateCreated%29%0D%0A+%3Falbumpages+%0D%0A+%3Falbumwidth%0D%0A+%3Falbumheight%0D%0A+%23%28GROUP_CONCAT%28DISTINCT%28%3Fimage%29%3B+separator%3D%22****%22%29+as+%3Falbumimages%29%0D%0A+WHERE+%7B%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+OPTIONAL%7B%3Falbum+schema%3AnumberOfPages+%3Falbumpages.%7D%0D%0A+%23%3Falbum+schema%3Aimage+%3Fa+.%0D%0A+%23%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A+%23CONCAT+fields%0D%0A+OPTIONAL%7B%3Falbum+schema%3Amaterial+%3Fmaterial.%7D+%0D%0A+OPTIONAL%7B%3Falbum+schema%3Adescription+%3Falbumdescr.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3AlocationCreated+%3Falbumlocation.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aidentifier+%3Finventory.%7D+%0D%0A+OPTIONAL%7B%3Falbum+schema%3Awidth+%3Falbumwidth.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aheight+%3Falbumheight.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3AdateCreated+%3Fdate.%7D%0D%0A+OPTIONAL%7B%3Falbum+schema%3Aauthor+%3Fowner.%7D+%0D%0A+%7D+%23GROUP+BY+%3Falbum++%3Falbumwidth+%3Falbumheight+%23%3Falbumpages%0D%0A+ORDER+BY+%3Falbum&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on&run=+Run+Query+)

### c) Alba and their contributions
_Retrieves 2096 results_
```
 SELECT DISTINCT ?album ?albumtitle ?contrib ?contribtitle WHERE { #
 ?contrib foaf:isPrimaryTopicOf/void:inDataset <http://data.bibliotheken.nl/id/dataset/rise-alba> .
 ?contrib schema:name ?contribtitle .
 ?contrib schema:isPartOf ?album .
 ?album schema:name ?albumtitle .
 } ORDER BY ?album
```
[Try this query](http://data.bibliotheken.nl/sparql?qtxt=+SELECT+DISTINCT+%3Falbum+%3Falbumtitle+%3Fcontrib+%3Fcontribtitle+WHERE+%7B+%23%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3Aname+%3Fcontribtitle+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+%7D+ORDER+BY+%3Falbum&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) -- [See query result](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=+SELECT+DISTINCT+%3Falbum+%3Falbumtitle+%3Fcontrib+%3Fcontribtitle+WHERE+%7B+%23%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3Aname+%3Fcontribtitle+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+%7D+ORDER+BY+%3Falbum&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) -- [Result as JSON](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=+SELECT+DISTINCT+%3Falbum+%3Falbumtitle+%3Fcontrib+%3Fcontribtitle+WHERE+%7B+%23%0D%0A+%3Fcontrib+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A+%3Fcontrib+schema%3Aname+%3Fcontribtitle+.%0D%0A+%3Fcontrib+schema%3AisPartOf+%3Falbum+.%0D%0A+%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A+%7D+ORDER+BY+%3Falbum&format=application%2Fsparql-results%2Bjson&timeout=0&debug=on&run=+Run+Query+)

### d) Number of contributions per album
According to [this query](http://data.bibliotheken.nl/sparql?default-graph-uri=&query=SELECT+DISTINCT+%3Falbum+%28count%28%3Falbumtitle%29+as+%3Faantalbijdragen%29+WHERE+%7B%0D%0A%3Finscription+foaf%3AisPrimaryTopicOf%2Fvoid%3AinDataset+%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fid%2Fdataset%2Frise-alba%3E+.%0D%0A%3Finscription+schema%3AisPartOf+%3Falbum+.%0D%0A%3Falbum+schema%3Aname+%3Falbumtitle+.%0D%0A%23%3Falbum+schema%3Aidentifier+%3FinventoryNumber.+%0D%0A%23%3Falbum+schema%3AdateCreated+%3FdateCreated+.%0D%0A%23%3Falbum+schema%3Aimage+%3Fa+.%0D%0A%23%3Fa+schema%3AcontentUrl+%3Fimage+.%0D%0A%7D+GROUP+BY+%3Falbum&format=text%2Fhtml&timeout=0&debug=on&run=+Run+Query+) there are 57 alba and 2096 contributions in the RoL set in data.bibliotheken.nl. This matches the numbers at b) and c)

## 2) Querying alba (+ contributions) through Wikidata query service
Copy-paste queries into [query.wikidata.org](https://query.wikidata.org/)
### a) All alba amicorum in data.bibliotheken.nl 
```
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT DISTINCT ?album ?albumtitle ?inventoryNumber ?dateCreated ?image WHERE {
  
  SERVICE <http://data.bibliotheken.nl/sparql>{
   ?inscription schema:isPartOf ?album .
   ?album schema:name ?albumtitle .
   ?album schema:identifier ?inventoryNumber. 
   ?album schema:dateCreated ?dateCreated .
   ?album schema:image ?a .
   ?a schema:contentUrl ?image .
}
}
ORDER BY ?album
limit 1000
```
[Try this query](https://w.wiki/49Ej) -- [See query result](https://w.wiki/49Ek) -- [Result as JSON](https://query.wikidata.org/sparql?query=PREFIX%20xsd%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2001%2FXMLSchema%23%3E%0APREFIX%20schema%3A%20%3Chttp%3A%2F%2Fschema.org%2F%3E%0APREFIX%20rdf%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23%3E%0APREFIX%20rdfs%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2000%2F01%2Frdf-schema%23%3E%0A%0ASELECT%20DISTINCT%20%3Falbum%20%3Falbumtitle%20%3FinventoryNumber%20%3FdateCreated%20%3Fimage%20WHERE%20%7B%0A%20%20%0A%20%20SERVICE%20%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fsparql%3E%7B%0A%20%20%20%3Finscription%20schema%3AisPartOf%20%3Falbum%20.%0A%20%20%20%3Falbum%20schema%3Aname%20%3Falbumtitle%20.%0A%20%20%20%3Falbum%20schema%3Aidentifier%20%3FinventoryNumber.%20%0A%20%20%20%3Falbum%20schema%3AdateCreated%20%3FdateCreated%20.%0A%20%20%20%3Falbum%20schema%3Aimage%20%3Fa%20.%0A%20%20%20%3Fa%20schema%3AcontentUrl%20%3Fimage%20.%0A%7D%0A%7D%0AORDER%20BY%20%3Falbum%0Alimit%201000&format=json)

### b) Contributions to an album with the name 'Kerwal' 
```
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX schema: <http://schema.org/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>

SELECT ?album  ?album_name ?bijdrage ?bijdrage_name ?auteur (GROUP_CONCAT(?desc;SEPARATOR = " ") as ?bijdrage_beschrijving) ?maakdatum ?maaklocatie ?afbeelding ?bijdrage_nummer WHERE {
  
  SERVICE <http://data.bibliotheken.nl/sparql>{
      ?album schema:name ?album_name .
      ?bijdrage schema:isPartOf ?album .
      ?bijdrage schema:description ?desc .
      ?bijdrage schema:name ?bijdrage_name .
      ?bijdrage schema:author ?auteur.
      ?bijdrage schema:dateCreated ?maakdatum.
      ?bijdrage schema:locationCreated ?maaklocatie.
      ?bijdrage schema:image [ schema:contentUrl ?afbeelding] .
      ?bijdrage schema:position ?bijdrage_nummer .
      FILTER Contains(?album_name,"Kerwal")
}
}
GROUP BY ?album ?bijdrage ?afbeelding ?auteur ?maakdatum ?maaklocatie ?album_name ?bijdrage_name ?bijdrage_nummer
ORDER BY xsd:integer(?bijdrage_nummer)
LIMIT 1000
```
[Try this query](https://w.wiki/49Ep) -- [See query result](https://w.wiki/49Eq) -- [Result as JSON](https://query.wikidata.org/sparql?query=PREFIX%20xsd%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2001%2FXMLSchema%23%3E%0APREFIX%20schema%3A%20%3Chttp%3A%2F%2Fschema.org%2F%3E%0APREFIX%20rdf%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F1999%2F02%2F22-rdf-syntax-ns%23%3E%0APREFIX%20rdfs%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2000%2F01%2Frdf-schema%23%3E%0A%0ASELECT%20%3Falbum%20%20%3Falbum_name%20%3Fbijdrage%20%3Fbijdrage_name%20%3Fauteur%20%28GROUP_CONCAT%28%3Fdesc%3BSEPARATOR%20%3D%20%22%20%22%29%20as%20%3Fbijdrage_beschrijving%29%20%3Fmaakdatum%20%3Fmaaklocatie%20%3Fafbeelding%20%3Fbijdrage_nummer%20WHERE%20%7B%0A%20%20%0A%20%20SERVICE%20%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fsparql%3E%7B%0A%20%20%20%20%20%20%3Falbum%20schema%3Aname%20%3Falbum_name%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AisPartOf%20%3Falbum%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Adescription%20%3Fdesc%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aname%20%3Fbijdrage_name%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aauthor%20%3Fauteur.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AdateCreated%20%3Fmaakdatum.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AlocationCreated%20%3Fmaaklocatie.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aimage%20%5B%20schema%3AcontentUrl%20%3Fafbeelding%5D%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aposition%20%3Fbijdrage_nummer%20.%0A%20%20%20%20%20%20FILTER%20Contains%28%3Falbum_name%2C%22Kerwal%22%29%0A%7D%0A%7D%0AGROUP%20BY%20%3Falbum%20%3Fbijdrage%20%3Fafbeelding%20%3Fauteur%20%3Fmaakdatum%20%3Fmaaklocatie%20%3Falbum_name%20%3Fbijdrage_name%20%3Fbijdrage_nummer%0AORDER%20BY%20xsd%3Ainteger%28%3Fbijdrage_nummer%29%0ALIMIT%201000&format=json)

### c) All contributions to alba amicorum in data.bibliotheken.nl
_Retrieves 3721 rows_
```
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX schema: <http://schema.org/>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>

SELECT DISTINCT ?album  ?album_name ?bijdrage ?bijdrage_name ?auteur (GROUP_CONCAT(DISTINCT ?desc;SEPARATOR = " ") as ?bijdrage_beschrijving) ?maakdatum ?maaklocatie ?afbeelding ?bijdrage_nummer WHERE {
  
  SERVICE <http://data.bibliotheken.nl/sparql>{
      ?album schema:name ?album_name .
      ?bijdrage schema:isPartOf ?album .
      ?bijdrage schema:description ?desc .
      ?bijdrage schema:name ?bijdrage_name .
      ?bijdrage schema:author ?auteur.
      ?bijdrage schema:dateCreated ?maakdatum.
      ?bijdrage schema:locationCreated ?maaklocatie.
      ?bijdrage schema:image [ schema:contentUrl ?afbeelding] .
      ?bijdrage schema:position ?bijdrage_nummer .
      #FILTER Contains(?album_name,"*")
  }
  } 
  GROUP BY ?album ?bijdrage ?afbeelding ?auteur ?maakdatum ?maaklocatie ?album_name ?bijdrage_name ?bijdrage_nummer
  ORDER BY ?album_name xsd:integer(?bijdrage_nummer)
```
[Try this query](https://w.wiki/49Et) -- [See query result](https://w.wiki/49Eu) -- [Result as JSON](https://query.wikidata.org/sparql?query=PREFIX%20xsd%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2001%2FXMLSchema%23%3E%0APREFIX%20schema%3A%20%3Chttp%3A%2F%2Fschema.org%2F%3E%0APREFIX%20foaf%3A%20%3Chttp%3A%2F%2Fxmlns.com%2Ffoaf%2F0.1%2F%3E%0A%0ASELECT%20DISTINCT%20%3Falbum%20%20%3Falbum_name%20%3Fbijdrage%20%3Fbijdrage_name%20%3Fauteur%20%28GROUP_CONCAT%28DISTINCT%20%3Fdesc%3BSEPARATOR%20%3D%20%22%20%22%29%20as%20%3Fbijdrage_beschrijving%29%20%3Fmaakdatum%20%3Fmaaklocatie%20%3Fafbeelding%20%3Fbijdrage_nummer%20WHERE%20%7B%0A%20%20%0A%20%20SERVICE%20%3Chttp%3A%2F%2Fdata.bibliotheken.nl%2Fsparql%3E%7B%0A%20%20%20%20%20%20%3Falbum%20schema%3Aname%20%3Falbum_name%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AisPartOf%20%3Falbum%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Adescription%20%3Fdesc%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aname%20%3Fbijdrage_name%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aauthor%20%3Fauteur.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AdateCreated%20%3Fmaakdatum.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3AlocationCreated%20%3Fmaaklocatie.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aimage%20%5B%20schema%3AcontentUrl%20%3Fafbeelding%5D%20.%0A%20%20%20%20%20%20%3Fbijdrage%20schema%3Aposition%20%3Fbijdrage_nummer%20.%0A%20%20%20%20%20%20%23FILTER%20Contains%28%3Falbum_name%2C%22%2a%22%29%0A%20%20%7D%0A%20%20%7D%20%0A%20%20GROUP%20BY%20%3Falbum%20%3Fbijdrage%20%3Fafbeelding%20%3Fauteur%20%3Fmaakdatum%20%3Fmaaklocatie%20%3Falbum_name%20%3Fbijdrage_name%20%3Fbijdrage_nummer%0A%20%20ORDER%20BY%20%3Falbum_name%20xsd%3Ainteger%28%3Fbijdrage_nummer%29&format=json)

