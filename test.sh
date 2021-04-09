staged_files="$1"

[ -z "$staged_files" ] ||
[[ $staged_files =~ [[:space:]]LanguageFi.php([[:space:]]|$) ]] ||
[[ $staged_files =~ [[:space:]]inflection.dataset([[:space:]]|$) ]] ||
[[ $staged_files =~ [[:space:]]Test.php([[:space:]]|$) ]] ||
[[ $staged_files =~ [[:space:]]test.sh([[:space:]]|$) ]] || exit 0

docker run --rm \
  -v "$PWD"/LanguageFi.php:/LanguageFi.php \
  -v "$PWD"/inflection.dataset:/inflection.dataset \
  -v "$PWD"/Test.php:/Test.php php:8.0.3-cli-alpine3.13 php /Test.php

# https://stackoverflow.com/a/12696899 (only POSIX ERE bash regex is portable)
