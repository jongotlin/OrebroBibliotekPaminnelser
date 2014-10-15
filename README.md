Örebro bibliotek är bra på många sätt men något fungerande påminnelsesystem har de inte. I det här projektet samlar du hela familjens boklån och får en epostpåminnelse när ni har lån som är på väg att gå ut.

![](https://raw.githubusercontent.com/jongotlin/OrebroBibliotekPaminnelser/master/screenshot.png)

Installera genom att klona repot och installera dependencies med `composer install`. Konfigurera `app/config/parameters.yml` och sätt upp cron att köra `php app/console check` en gång per dag.
