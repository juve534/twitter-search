#/bin/sh

set -a
source .env
set +a

echo "アクセスキーを指定してください"
echo -n 'aws_access_key_id: '
read aws_access_key_id

echo "シークレットアクセスキーを指定してください"
echo -n 'aws_secret_access_key: '
read aws_secret_access_key

if [[ -z "${aws_access_key_id}" ]];then
  echo 'アクセスキーが指定されてません'
  exit 1
fi

if [[ -z "${aws_secret_access_key}" ]];then
  echo 'シークレットアクセスキーが指定されてません'
  exit 1
fi

docker-compose exec app serverless config credentials --provider aws --key $aws_access_key_id --secret $aws_secret_access_key