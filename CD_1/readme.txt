****************************************************************
Adatbazis könyvtár
****************************************************************
Puzzle - az adatbázis fájljai
puzzle.sql - sql_dump
puzzle.php - az adatbázis telepítõ fájlja


****************************************************************
Flash könytár
****************************************************************
ActionScript.pdf - A játékhoz tartozó ActionScript 
pic.fla - a játék forrásfájlja
pic.swf - a futtatható játék (önmagában nem mûködik, mert szüksége van az adatbázisból kapott adatokra)
pic_draw.fla - a rajzoló program forrásfájlja
pic_draw.swf - a rajzoló program futtatható verziója (Az Admin oldalon, az új játék felvitelénél szükséges kódot a balszélsõ, "Publish" feliratú gombbal lehet kiíratni és vágólapra másolni.)


****************************************************************
WWW könyvtár és telepítés
****************************************************************
A www könyvtár tartalmát egy az egyben fel kell másolni a webszerverre.

Az Inc könyvtárban található config_server.php-ét át kell írni az adott webszerver paramétereinek megfelelõen.
(Adatbázis elérés és esetleg a Smarty BASE_DIR)

Az adatbázis telepítése:

1. mód:
Az Adatbazis/Puzzle könyvtár tartalmát közvetlenül bemásoljuk az adatbázisszerverre.

2. mód:
Az Adatbazis/Puzzle/puzzle.sql fájl tartalmát a phpMyAdmin-ban SQL parancsként lefuttatjuk.

3. mód:
Az Adatbazis/Puzzle/puzzle.php fájlt felmásoljuk a webszerverre és lefuttatjuk. 

