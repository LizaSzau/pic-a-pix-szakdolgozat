****************************************************************
Adatbazis k�nyvt�r
****************************************************************
Puzzle - az adatb�zis f�jljai
puzzle.sql - sql_dump
puzzle.php - az adatb�zis telep�t� f�jlja


****************************************************************
Flash k�nyt�r
****************************************************************
ActionScript.pdf - A j�t�khoz tartoz� ActionScript 
pic.fla - a j�t�k forr�sf�jlja
pic.swf - a futtathat� j�t�k (�nmag�ban nem m�k�dik, mert sz�ks�ge van az adatb�zisb�l kapott adatokra)
pic_draw.fla - a rajzol� program forr�sf�jlja
pic_draw.swf - a rajzol� program futtathat� verzi�ja (Az Admin oldalon, az �j j�t�k felvitel�n�l sz�ks�ges k�dot a balsz�ls�, "Publish" felirat� gombbal lehet ki�ratni �s v�g�lapra m�solni.)


****************************************************************
WWW k�nyvt�r �s telep�t�s
****************************************************************
A www k�nyvt�r tartalm�t egy az egyben fel kell m�solni a webszerverre.

Az Inc k�nyvt�rban tal�lhat� config_server.php-�t �t kell �rni az adott webszerver param�tereinek megfelel�en.
(Adatb�zis el�r�s �s esetleg a Smarty BASE_DIR)

Az adatb�zis telep�t�se:

1. m�d:
Az Adatbazis/Puzzle k�nyvt�r tartalm�t k�zvetlen�l bem�soljuk az adatb�zisszerverre.

2. m�d:
Az Adatbazis/Puzzle/puzzle.sql f�jl tartalm�t a phpMyAdmin-ban SQL parancsk�nt lefuttatjuk.

3. m�d:
Az Adatbazis/Puzzle/puzzle.php f�jlt felm�soljuk a webszerverre �s lefuttatjuk. 

