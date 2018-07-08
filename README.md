# Json Extract

```
composer install
composer build
```

```
$ php gen.php | php json-extract.phar o=0
{"foo":"bar"}
{"foo":"bar"}
```

```
$ php gen.php | php json-extract.phar o=1
{"file":"/home/will/src/github.com/will1471/json-extract/gen.php","line":12,"class":null,"function":null}
{"file":"/home/will/src/github.com/will1471/json-extract/gen.php","line":13,"class":null,"function":null}
```

Problems
```
$ echo '{ {"foo":"bar"}' | php json-extract.phar o=0
{"foo":"bar"}

$ echo '{" {"foo":"bar"}' | php json-extract.phar o=0
```