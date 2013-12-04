# Titanium Systems OÜ test-ülesanne


## Keskkond

Testimiseks kasutasin:

- PHP 5.5.2 (+ PDO andmebaasimoodul).
- MySQL 5.1.70
- phpunit 3.7.20

Kuna kasutasin ohtralt PHP 5.4 uut massiivide süntaksit, siis vanemate
versioonidega kindlasti ei tööta, samas midagi PHP 5.5 spetsiifilist
ei tohiks olla, seega teoorias peaks PHP 5.4 peal jooksma.


## Installeerimine

Kopeeri repositoorium:

    git clone https://github.com/nene/titanium-test-assignment.git
    cd titanium-test-assignment/

Loo uus MySQL-i andmebaas (näiteks nimega `car_prices`).

Kirjuta andmebaasiga ühendumise seaded faili `db-config.ini`,
kasutades mallina `db-config-example.ini` faili.

    cp db-config-example.ini db-config.ini
    vi db-config.ini

Loo andmebaasistruktuur failist `db/schema.sql`:

    cd db/
    mysql car_prices < schema.sql

Seejärel lae andmed failist `db/dump.sql`:

    mysql car_prices < dump.sql

Alternatiivina võid importida ka otse algsetest CSV failidest:

    php import.php a.csv b.csv

Ongi valmis :)


## Testimine

Unit-testid on `test/` kataloogis, nende jooksutamiseks:

    phpunit test/

Kogu rakenduse testimiseks on `test.html` leht, mille kaudu saab
mugavalt XML päringuid saata.


## API

`index.php` ootab POST päringuid, milles parameeter `query` sisaldab
tegelikku päringu XML-i.

Hetkel on toetatud vaid `SearchCarRQ` päring:

    <SearchCarRQ>
        <Country name='Austria'>
            <City>Linz</City>
            <City>Salzburg</City>
        </Country>
        <Country name='Portugal'>
            <City>Faro</City>
        </Country>
    </SearchCarRQ>

Selle vastuseks on igas riigis oleva linna viis kõige odavamat autot
sorteerituna hinna järgi kasvavalt:

    <SearchCarRS>
        <Country name='Austria'>
            <City name='Linz'>
                <Car price='34.33'>BMW X3</Car>
                <Car price='34.34'>Opel Zafira</Car>
                <Car price='34.40'>Renault Laguna</Car>
                <Car price='35.00'>Volvo S40</Car>
                <Car price='40.00'>Mercedes-Benz E Class</Car>
            </City>
            <City name='Salzburg'>
                <Car price='31.03'>Opel Zafira</Car>
                <Car price='32.00'>Skoda Octavia</Car>
                <Car price='35.23'>Volkswagen Lupo</Car>
                <Car price='47.12'>Fiat Panda</Car>
                <Car price='49.11'>Fiat Bravo</Car>
            </City>
        </Country>
        <Country name='Portugal'>
            <City name='Faro'>
                <Car price='40.12'>BMW Z4 Coupe</Car>
                <Car price='42.12'>BMW X3</Car>
                <Car price='43.00'>Opel Astra</Car>
                <Car price='45.23'>Volvo S40</Car>
                <Car price='50.00'>Skoda Octavia</Car>
            </City>
        </Country>
    </SearchCarRS>

Kui päringu töötlemisel tekib probleeme, saadetakse vastuseks viga
kirjeldav XML.  Näiteks vigase XML-i korral:

    <Error>
        <Msg>String could not be parsed as XML</Msg>
    </Error>

Päringu XML-i struktuuri kontrollitakse rangelt.  `<SearchCarRQ>` peab
sisaldama 1 või rohkem `<Country>` elementi, mis peab sisaldama 1 või
rohkem `<City>` elementi jne.  Seega, soovides mõne riigi kohta mitte
päringut sooritada, tuleb vastav `<Country>` element ära jätta, mitte
näiteks saata tühi element.


## Arhitektuur

Rakenduse andmevoog on järgmine:

* Sissetulev XML string parsitakse `SimpleXML` abil.

* Parsitud XML valideeritakse lihtsakoelise *schema* vastu
  `XmlValidator` klassi abil.

* Nüüd kus saame kindlad olla päringu korrektses formaadis,
  konverteeritakse see lihtsaks PHP massiivide struktuuriks, mida
  rakenduse siseselt kasutada.

* Iga riigi ja linna kohta päringu objektis tehakse andmebaasi päring,
  ning luuakse uus lihtne PHP massiivide struktuur, mis kirjeldab
  vastust.

* Vastuse andmestruktuur konverteeritakse `SimpleXML` abil XML-i
  stringiks ning visatakse väljundisse.

Kogu selle protsessimise käigus võib ette tulla erindeid, mis
rakenduse kõige kõrgemal tasandil kinni püütakse ning XML formaadis
kasutajale saadetakse.


## Kommentaarid

* XML-i struktuuri valideerimiseks oleks vast parem kasutada mõnda
  olemasolevat *schema* validaatorit.  Kuna ma aga pole erinevate
  XML-i *schema* kirjeldamise vahenditega eriti tuttav ning see teema
  vajaks pikemat süvenemist, otsustasin kokku klopsida pisikese
  kodukootud validaatori.

* Lihtsuste mõttes oleks võinud kogu selle XML -> PHP massiivid -> XML
  transformeerimise ära jätta, ning anda päringut tegevale klassile
  ette `SimpleXMLElement` instantsi ning samuti ka väljundi
  koostamisel.  Kuna `SimpleXML` on PHP *built-in* moodul, siis pole
  sellest sõltumine ise-enesest eriti probleemne.  Kuid see lähenemine
  oleks kogu rakenduse loogika liialt tihedasti sidunud XML-i
  struktuuriga, samuti raskendanuks see testimist.

  Samas mind üksjagu häirib see väga spetsiifiline kood, mis selle
  XML-i muudamisega tegeleb.  Kui päringuid tekib juurde, siis on iga
  päringu jaoks vaja posu koodi, mis tegeleb selle XML-ist ja XML-i
  parsimisega, ja see tundub mulle üks suur ja üleliigne vaev
  (*accidental complexity*, mis tuleneb meie lahenduse ja mitte
  ülesande enda keerukusest).

  Üks variant oleks teha konverter, mis suvalise XML-i PHP massiivide
  struktuuriks muudaks (ja vastupidi).  Sedaviisi saaksime lahti
  päringu-spetsiifilisest XML-i muundamise loogikast ning ühtlasi ei
  seoks end tihedalt ka SimpleXML-i apiga.  Kahjuks oleksime sedasi
  aga endiselt tihedasti seotud XML-i struktuuriga - sedasi
  genereeritud PHP andmestruktuur oleks üsna lähedane XML-i enda
  struktuurile.  Samuti saaks see takistuseks kui sooviksime lisada
  näiteks JSON-i API toe.

  Teine variant oleks kasutada sarnast loogikat nagu XML-i
  valideerimisel.  Selle asemel, et kirjutada algoritm spetsiifilise
  XML-i struktuuri konverteerimiseks, tuleks kuidagi kirjeldada
  reeglid vastava transformatsiooni jaoks.  Sedasi peaksime iga uue
  päringu jaoks kirjutama vaid väikese definitsiooni mille järgi XML
  PHP massiivideks muudetaks.  Iseküsimus muidugi, kuidas sellist asja
  realiseerida nõnda, et need kirja pandavad transformatsiooni-reeglid
  poleks niisama keerukad kui sama asja algoritmiliselt väljendamine.

* Ülesandes on palutud pöörata tähelepanu laiendatavusele uute
  päringutega.  Proovisin seda ka teha, kuigi minu üldine seisukoht
  on, et päriselt saab sellega tegeleda alles siis kui meil on
  vähemalt kaks päringut.  Kui sellist reaalselt ühte päringut
  haldavat süsteemi üritada kirjutada nii, nagu see peaks haldama
  kahte, on selge oht üle-arendamise osas, ning pahatihti vales
  suunas.
