

SELECT DISTINCT ?person ?personLabel ?personDescription ?gender ?image WHERE { 
  ?person wdt:P31 wd:Q5;
          wdt:P21 ?gender;
          wdt:P3919 wd:Q72752496. # contributed to creative work (P3919) Album amicorum of Jacob Heyblocq (Q72752496)
  OPTIONAL{?person wdt:P18 ?image.}
  SERVICE wikibase:label { bd:serviceParam wikibase:language "en,nl". }
   
} 
ORDER BY ?person
