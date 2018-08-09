# 3. Como Usar

É muito fácil usar o Light Obfuscator. Basicamente existem duas formas:

* Ofuscar um arquivo individual
* Ofuscar um diretório inteiro

## 3.1. Ofuscando um único arquivo

Para ofuscar um único arquivo PHP, usa-se a classe ObfuscateFile,
fornecendo o caminho completo até o arquivo PHP a ser procesado e também a localização do resultado ofuscado.

* O método **obfuscateFile()** marca o arquivo para ofuscação;
* O método **save()** ofusca efetivamente, salvando na localização especificada.

```php
$ob = new LightObfuscator\ObfuscateFile;
$ob->obfuscateFile('/var/www/app/projeto/arquivo.php');
$ob->save('/var/www/app/projeto/ofuscado.php');
```

```php
/*
 * Arquivo /var/www/app/index.php
 */

include 'projeto/ofuscado.php';
```

## 3.2. Ofuscando um diretório

Para ofuscar um diretório inteiro, usa-se a classe ObfuscateDirectory,
fornecendo o caminho completo até o diretório a ser procesado e também a localização do resultado ofuscado.

* O método **obfuscateDirectory()** marca o diretório para ofuscação;
* O método **saveDirectory()** ofusca efetivamente, salvando na localização especificada.

```php
$ob = new LightObfuscator\ObfuscateDirectory;
$ob->obfuscateDirectory('/var/www/app/projeto');
$ob->saveDirectory('/var/www/app/projeto-ofuscado');
```

O diretório resultante conterá um arquivo chamado 'autoloader.php'
que deve ser incluido em um arquivo PHP normal para que tudo funcione normalmente.

```php
/*
 * Arquivo /var/www/app/index.php
 */

include 'projeto-ofuscado/autoloader.php';
```

> *Nota:* a estrutura de diretórios mostrada acima é apenas um exemplo. Qualquer estrutura pode ser usada.

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
