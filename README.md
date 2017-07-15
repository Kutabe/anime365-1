**[На русском (same text in russian)](#на-русском)**

# Anime 365

New version of [Anime 365](smotret-anime.ru), an website that features easy to use anime catalog and ability to watch anime online.

Backend is GraphQL API written using Yii 2, PHP 7.1 and PosgreSQL 9.6. We plan to use vue.js for frontend.

Because previous version of website was written using Yii 1, we decided to start this project from scratch and adding code from previous version step-by-step along with rewriting it.


## Quick start (Windows)
Download [ready to use package](https://smotret-anime.ru/content/dev_package.zip) with all required tools (PHP 7.1, nginx with php-fpm, PostgreSQL 9.6).
1. Unzip package.
2. Run Tools/Clone_project.cmd (this will clone project from git and install dependencies)
3. Run Tools/Import_db.cmd (this will download SQL dump and import it).
4. Run start.cmd and open [http://localhost/](http://localhost/) to check.

We strongly suggest you to use "ready to use" package even if you already have some software from it. But if you still want to install it manually - see below.

## Quick start (Linux, MacOS, Windows) (manually)

[Open instructions to setup project manually](INSTALL_EN.md)

# На русском

Это новая версия [Anime 365](smotret-anime.ru), сайта с удобным каталогом аниме и онлайн просмотром.

Бекенд представлен в виде GraphQL API и написан на Yii 2, используя PHP 7.1 и PosgreSQL 9.6. Для фронтенда планируется vue.js.

Так как предыдущая версия сайта была написана на Yii 1, то было решено начать проект с чистого листа, постепенно внедряя готовые решения из предыдущей версии (заодно дорабатывая их).

## Быстрый старт (Windows)
Скачайте [готовый пакет](https://smotret-anime.ru/content/dev_package.zip) со всем необходимым для разработки (уже настроенные PHP 7.1, nginx с php-fpm, PostgreSQL 9.6).
1. Разархивируйте пакет в удобную для себя папку.
2. Запустите Tools/Clone_project.cmd (склонирует актуальную версию из git и установит зависимости)
3. Запустите Tools/Import_db.cmd (скачает актуальный дамп базы и импортирует его).
4. Запустите start.cmd и зайдите на [http://localhost/](http://localhost/), чтобы проверить, что всё работает.

Советуем вам использовать готовый пакет даже если у вас уже установлена часть софта. Однако если вы хотите установить всё вручную, смотрите следующий пункт.

## Быстрый старт (Linux, MacOS, Windows) (вручную)

[Открыть инструкцию по настройке вручную](INSTALL_RU.md)