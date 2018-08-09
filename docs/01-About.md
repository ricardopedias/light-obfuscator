# 1. Light Obfuscator

## 1.1. Sobre o Light Obfuscator

O Light Obfuscator é uma biblioteca bem testada que atualmente é executada em vários projetos reais. Surgiu da necessidade de ocultar o código, que possuía direitos autorais, e precisava ser executando no servidor de um cliente. Outra necessidade importante era que o código resultante da ofuscação fosse o mais rápido possível, custando o mínimo de recursos para o servidor executar.

Originalmente a biblioteca foi desenvolvida para o PHP 5.2, evoluindo para o 5.6 e executando sem problemas em todo este período. Com o advento do PHP 7, a performance e a agilidade aumentou consideravelmente.

## 1.2. Performance e Segurança

A ofuscação gerada pela biblioteca é autocontida, ou seja, não precisa que sejam instaladas extensões adicionais ou softwares no servidor. Todo o processo é executado pelo próprio PHP.

Embora a biblioteca seja bem feita e desenvolvida com o máximo de qualidade, não é de se esperar que o código resultante do processo de ofuscação possua exatamente a mesma performance de uma código aberto (normal) do PHP, pois existe uma camada a mais acontecendo no processo, onde a ofuscação é desfeita em tempo de execução.

Em relação a segurança, optou-se pela performance mais do que pela impossibilidade da desofuscação manual. Embora seja bem complicado para a maioria dos programadores, é "possível" desofuscar o código através de conhecimento somado com a tentativa e erro. Mas isso se torna totalmente inviável uma vez que grande tempo deve ser dispensado para isso, haja visto que cada arquivo possui rotinas diferentes de reversão da ofuscação, rotinas geradas randomicamente a cada ofuscação.

## 1.3. As versões da biblioteca

O método de versionamento utilizado para as evoluções da biblioteca seguem as regras da [Semantic Versioning](https://semver.org/lang/pt-BR/), uma especificação bastante utilizada na industria de Softwares, criada por Thom Preston Werner, criador do Gravatars e Co-Fundador do Github.

O formato das versões seguem a seguinte convenção:
```
X.Y.Z
```
Onde:

* X (major version): Muda quando temos incompatibilidade com versões anteriores.
* Y (minor version): Muda quando temos novas funcionalidades em nosso software.
* Z (patch version): Muda quando temos correções de bugs lançadas.

Explicando melhor:

**X**: é incrementado sempre que alterações **incompatíveis** com as versões anteriores da API forem implementadas. Por exemplo, sendo a versão atual 1.0.5, uma implementação precisou alterar campos no banco de dados, então a próxima versão será 2.0.0;

**Y**: é incrementado sempre que forem implementadas novas funcionalidades **compatíveis** e que não afetem o funcionamento normal da aplicação. Por exemplo, sendo a versão atual 1.9.5, uma nova funcionalidade foi adicionada, então a próxima versão será 1.10.0. Note que a versão do último número foi zerada para seguir a especificação da Semantic Versioning;

**Z**: é incrementado sempre que forem implementadas correções de falhas (bug fixes) que não afetem o funcionamento normal da aplicação. Por exemplo, sendo a versão atual 1.0.9, uma correção foi feita, gerando uma refatoração que otimizou o código, então a próxima versão será 1.0.10.

## Sumário

1. [Sobre](01-About.md)
2. [Instalação](02-Installation.md)
3. [Como Usar](03-Usage.md)
