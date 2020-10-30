<?php


namespace App\Utils;


use Illuminate\Support\Facades\Redis;

/**
 * @Author 志在卓越 2020.10
 * 测试环境 Redis 3.0.504 , PHP 7.3.4 , Laravel 6.19.1 , Client phpredis
 * Class RedisUtil
 * @package App\Utils
 */
class RedisUtil
{
    /************** Key ***************/

    /**
     * 判断是否存在
     * @param string $key
     * @return mixed
     */
    public function exists(string $key): bool
    {
        return Redis::exists($key);
    }

    /**
     * 返回类型
     * none(key不存在) int(0)
     * string(字符串) int(1)
     * list(列表) int(3)
     * set(集合) int(2)
     * zset(有序集) int(4)
     * hash(哈希表) int(5)
     * @param string $key
     * @return int
     */
    public function type(string $key): int
    {
        return Redis::type($key);
    }

    /**
     * 删除
     * @param string $key
     * @return bool
     */
    public function del(string $key): bool
    {
        $res = Redis::del($key);
        if ($res > 0) {
            return true;
        } else {
            return false;
        }
    }


    /************** String ***************/

    /**
     * 保存
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        return Redis::set($key, $value);
    }

    /**
     * 带有效期保存
     * @param string $key
     * @param string $value
     * @param int $timeout 有效期（单位秒）
     * @return bool
     */
    public function setEx(string $key, string $value, int $timeout): bool
    {
        return Redis::setex($key, $timeout, $value);
    }

    /**
     * 当key不存在的时候才保存
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setNx(string $key, string $value): bool
    {
        return Redis::setnx($key, $value) === 1 ? true : false;
    }

    /**
     * 同时保存多个 key-value
     * @param array $keyValue
     * @return bool
     */
    public function mSet(array $keyValue): bool
    {
        return Redis::mset($keyValue);
    }

    /**
     * 同时保存多个key-value,当所有key都不存在的时候才保存,即使只有一个key已存在也不行
     * @param array $keyValue
     * @return bool
     */
    public function mSetNx(array $keyValue): bool
    {
        return Redis::msetnx($keyValue);
    }

    /**
     * 将value追加到原来key的值之后，如果key不存在则创建一个
     * @param string $key
     * @param string $value
     * @return int 返回字符串长度
     */
    public function appEnd(string $key, string $value): int
    {
        return Redis::append($key, $value);
    }

    /**
     * 获取
     * @param string $key
     * @return mixed 返回对应的值，如果不存在则返回null
     */
    public function get(string $key)
    {
        return Redis::get($key);
    }

    /**
     * 获取多个key的值 【使用方法 mGet('key1','key2','key3')】
     * @param string ...$key
     * @return array 当其中某个没有则该值为null
     */
    public function mGet(string ...$key): array
    {
        return Redis::mget($key);
    }

    /**
     * 将给定key的值设为value，并返回key的旧值。
     * @param string $key
     * @param string $value
     * @return bool|string 没有旧值(表示之前不存在该key，则新创建一个)则返回空字符串(""),如果返回false则是失败的，
     */
    public function getSet(string $key, string $value)
    {
        $type = $this->type($key);
        $oldValue = Redis::getset($key, $value);
        if ($type === 0) {
            return "";
        }
        if ($type === 1) {
            return $oldValue;
        }
        return false;
    }

    /**
     * 返回字符串字符的长度，返回false则代表没有该key或非string类型
     * @param string $key
     * @return bool|int
     */
    public function strLen(string $key)
    {
        if (1 === $this->type($key)) {
            return (int)Redis::strlen($key);
        }
        return false;
    }

    /**
     * 将key中储存的数字值 +1 。
     * @param string $key
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function incr(string $key)
    {
        return Redis::incr($key);
    }

    /**
     * 将key中储存的数字值 +{$increment}
     * @param string $key
     * @param int $increment
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function incrBy(string $key, int $increment)
    {
        return Redis::incrby($key, $increment);
    }

    /**
     * 将key中储存的数字值 -1
     * @param string $key
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function decr(string $key)
    {
        return Redis::decr($key);
    }

    /**
     * 将key中储存的数字值 -{$decrement}
     * @param string $key
     * @param int $decrement
     * @return bool|int 如果返回false 说明value不是数字类型字符
     */
    public function decrBy(string $key, int $decrement)
    {
        return Redis::decrby($key, $decrement);
    }

    /************** Hash ***************/

    /**
     * 将哈希表key中的域field的值设为value (当key不存在则创建，当域field已经存在于哈希表中，则旧值被覆盖)
     * @param string $key
     * @param string $field
     * @param $value
     * @return bool
     */
    public function hSet(string $key, string $field, $value): bool
    {
        $hSetRes = Redis::hset($key, $field, $value);
        return ($hSetRes === 1 || $hSetRes === 0) ? true : false;
    }

    /**
     * 将哈希表key中的域field的值设为value (当key不存在则创建，当域field已经存在于哈希表中，则一样无效)
     * @param string $key
     * @param string $field
     * @param $value
     * @return bool
     */
    public function hSetNx(string $key, string $field, $value): bool
    {
        return Redis::hsetnx($key, $field, $value) === 1 ? true : false;
    }

    /**
     * 同时将多个field-value(域-值)对设置到哈希表key中【使用方法：hMSet('key_name', ['name' => '志在卓越', 'from' => 'MaoMing']))】
     * 如果key不存在,则创建。域field不存在则创建field，field存在则会覆盖哈希表中已存在的域field （记住只是覆盖某个field不是整个key）
     * @param string $key
     * @param array $fieldValue
     * @return bool
     */
    public function hMSet(string $key, array $fieldValue): bool
    {
        $type = $this->type($key);
        if ($type === 0 || $type === 5) {
            return Redis::hmset($key, $fieldValue);
        } else {
            return false;
        }
    }

    /**
     * 返回哈希表key中给定域field的值。(当给定域field不存在或是给定key不存在时，返回null)
     * @param string $key
     * @param string $field
     * @return mixed
     */
    public function hGet(string $key, string $field)
    {
        $hGetRes = Redis::hget($key, $field);
        return $hGetRes === false ? null : $hGetRes;
    }

    /**
     * 返回哈希表key中，一个或多个给定域的值【使用方法 hMGet('key_name','field1','field2','field3')】
     * 如果整个key都不存在，则里面的所有值都是null
     * 如果给定的某个域field不存在于哈希表，那么某个就是null值
     * @param string $key
     * @param string ...$field
     * @return mixed
     */
    public function hMGet(string $key, string ...$field): array
    {
        $result = Redis::hmget($key, $field);
        return array_map(function ($value) {
            return $value === false ? null : $value;
        }, $result);
    }

    /**
     * 返回哈希表key中，所有的域和值 （当key不存在则返回空数组）
     * @param string $key
     * @return array
     */
    public function hGetAll(string $key): array
    {
        return Redis::hgetall($key);
    }

    /**
     * 删除哈希表key中的一个或多个指定域field，不存在的域将被忽略。
     * @param string $key
     * @param string ...$field
     * @return int 返回删除成功的个数
     */
    public function hDel(string $key, string ...$field): int
    {
        return Redis::hdel($key, ...$field);
    }

    /**
     * 返回哈希表key中域的数量
     * @param string $key
     * @return int
     */
    public function hLen(string $key): int
    {
        return Redis::hlen($key);
    }

    /**
     * 查看哈希表key中，给定域field是否存在
     * @param string $key
     * @param string $field
     * @return bool
     */
    public function hExists(string $key, string $field): bool
    {
        return Redis::hexists($key, $field);
    }

    /**
     * 为哈希表key中的域field的值 +{$increment} ,$increment可以负数
     * @param string $key
     * @param string $field
     * @param int $increment
     * @return int|bool 如果返回false,说明值不是数字类型字符
     */
    public function hIncrBy(string $key, string $field, int $increment)
    {
        return Redis::hincrby($key, $field, $increment);
    }

    /**
     * 返回哈希表key中的所有域field,如果key不存在则返回空数组
     * @param string $key
     * @return array
     */
    public function hKeys(string $key): array
    {
        return Redis::hkeys($key);
    }

    /**
     * 返回哈希表key中的所有值value,如果key不存在则返回空数组
     * @param string $key
     * @return array
     */
    public function hValues(string $key): array
    {
        return Redis::hvals($key);
    }

    /************** List ***************/

    /**
     * 将一个或多个值value插入到列表key的表头。如果key不存在,则创建
     * 如果有多个value值，那么各个value值按从左到右的顺序依次插入到表头
     * 也就是说 插入 a b c d ，则是 d c b a
     * @param string $key
     * @param string ...$value
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function lPush(string $key, string ...$value)
    {
        return Redis::lpush($key, ...$value);
    }

    /**
     * 将值value插入到列表key的表头，当且仅当key存在并且是一个列表(当key不存在时,则无效)
     * @param string $key
     * @param string $value
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function lPushX(string $key, string $value)
    {
        return Redis::lpushx($key, $value);
    }

    /**
     * 将一个或多个值value插入到列表key的表尾,如果key不存在,则创建
     * 如果有多个value值，那么各个value值按从左到右的顺序依次插入到表尾
     * 也就是说 插入 a b c d ,则 a b c d
     * @param string $key
     * @param string ...$value
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function rPush(string $key, string ...$value)
    {
        return Redis::rpush($key, ...$value);
    }


    /**
     * 将值value插入到列表key的表尾,当且仅当key存在并且是一个列表(当key不存在，则无效)
     * @param string $key
     * @param string $value
     * @return int|bool 返回list的长度,如果返回 false,代表key存在但不是list类型
     */
    public function rPushX(string $key, string $value)
    {
        return Redis::rpushx($key, $value);
    }

    /**
     * 移除并返回列表key的头元素
     * @param string $key
     * @return mixed 如果返回false则代表该key不存在或不是list类型
     */
    public function lPop(string $key)
    {
        return Redis::lpop($key);
    }

    /**
     * 移除并返回列表key的尾元素
     * @param string $key
     * @return mixed 如果返回false则代表该key不存在或不是list类型
     */
    public function rPop(string $key)
    {
        return Redis::rpop($key);
    }

    /**
     * 是 lpop 命令的阻塞版本 ，移除并返回列表key的头元素，多个key时候则先后顺序依次检查，即 key1,key2,key3
     * 【使用方法 bLPop(5,'key1','key2','key3')】
     * @param int $timeout (单位秒) 0表示阻塞时间可以无限期延长，意思是没有数据的时候等待执行时间
     * @param string ...$key
     * @return mixed 返回null不存在key或者key不是list类型
     */
    public function bLPop(int $timeout, string ...$key)
    {
        return Redis::blpop($key, $timeout);
    }

    /**
     * 是 rpop 命令的阻塞版本，移除并返回列表key的尾元素，多个key时候则先后顺序依次检查，即 key1,key2,key3
     * 【使用方法 bRPop(5,'key1','key2','key3')】
     * @param int $timeout (单位秒) 0表示阻塞时间可以无限期延长，意思是没有数据的时候等待执行时间
     * @param string ...$key
     * @return mixed 返回null不存在key或者key不是list类型
     */
    public function bRPop(int $timeout, string ...$key)
    {
        return Redis::brpop($key, $timeout);
    }

    /**
     * 返回list的长度
     * @param string $key
     * @return int
     */
    public function lLen(string $key): int
    {
        if (3 === $this->type($key)) {
            return (int)Redis::llen($key);
        }
        return 0;
    }

    /**
     * 返回列表key中指定区间内的元素，区间以偏移量start和stop指定 ，索引从0开始
     * 如果 -1表示列表的最后一个元素，-2表示列表的倒数第二个元素
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return array
     */
    public function lRange(string $key, int $start, int $stop): array
    {
        $res = Redis::lrange($key, $start, $stop);
        return !$res ? [] : $res;
    }

    /**
     * 根据参数count的值，移除列表中与参数value相等的元素
     * @param string $key
     * @param int $count 移除数量为count的绝对值，count正数则从头到尾搜索，负数则从尾到头搜索
     * @param string $value
     * @return int 被移除元素的数量（不存在的key则是0）
     */
    public function lRem(string $key, int $count, string $value): int
    {
        return (int)Redis::lrem($key, $count, $value);
    }

    /**
     * 将列表key下标为index的元素的值设置为为value
     * @param string $key
     * @param int $index
     * @param string $value
     * @return bool
     */
    public function lSet(string $key, int $index, string $value): bool
    {
        return Redis::lset($key, $index, $value);
    }

    /**
     * 让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除
     * 以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
     * 以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推
     * @param string $key
     * @param int $start 开始
     * @param int $stop 结束
     * @return bool
     */
    public function lTrim(string $key, int $start, int $stop): bool
    {
        return Redis::ltrim($key, $start, $stop);
    }

    /**
     * 返回列表key中，下标为index的元素
     * @param string $key
     * @param int $index
     * @return mixed 返回false则获取失败的
     */
    public function lIndex(string $key, int $index)
    {
        return Redis::lindex($key, $index);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之前
     * @param string $key
     * @param string $pivot
     * @param string $value
     * @return mixed 返回插入操作完成之后，list的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0，如果返回false则不是list类型
     */
    public function lInsertBefore(string $key, string $pivot, string $value)
    {
        return Redis::linsert($key, "BEFORE", $pivot, $value);
    }

    /**
     * 将值value插入到列表key当中，位于值pivot之后
     * @param string $key
     * @param string $pivot
     * @param string $value
     * @return mixed 返回插入操作完成之后，list的长度。如果没有找到pivot，返回-1。如果key不存在或为空列表，返回0，如果返回false则不是list类型
     */
    public function lInsertAfter(string $key, string $pivot, string $value)
    {
        return Redis::linsert($key, "AFTER", $pivot, $value);
    }

    /**
     * 将列表{$sourceKey}中的最后一个元素(尾元素)弹出，并插入到列表{$destinationKey}里作为头元素
     * 如果{$sourceKey}和{$destinationKey}相同，则列表中的表尾元素被移动到表头，并返回该元素
     * 假如{$sourceKey}列表有元素a, b, c , d {$destinationKey}列表有元素x, y, z,执行完之后{$sourceKey}就变成a, b, c，而{$destinationKey}就变成d, x, y, z
     * @param string $sourceKey
     * @param string $destinationKey
     * @return mixed 返回{$sourceKey}弹出的元素，如果返回false，可能是{$sourceKey}与{$destinationKey}不是list类型或{$sourceKey}不存在
     */
    public function rPopLPush(string $sourceKey, string $destinationKey)
    {
        return Redis::rpoplpush($sourceKey, $destinationKey);
    }

    /**
     * 是rpoplpush的阻塞版本
     * @param string $sourceKey
     * @param string $destinationKey
     * @param int $timeout 等待时间（单位秒），也就是说当列表{$sourceKey}为空时,等待执行时间。0表示阻塞时间可以无限期延长
     * @return mixed
     */
    public function bRPopLPush(string $sourceKey, string $destinationKey, int $timeout)
    {
        return Redis::brpoplpush($sourceKey, $destinationKey, $timeout);
    }

    /************** Set ***************/

    /**
     * 将一个或多个member元素加入到集合key当中，已经存在于集合的{$member}元素将被忽略
     * @param string $key
     * @param string ...$member
     * @return int|bool 返回添加到集合中的新元素的数量，如果返回false说明key不是Set类型
     */
    public function sAdd(string $key, string ...$member)
    {
        return Redis::sadd($key, ...$member);
    }

    /**
     * 移除集合key中的一个或多个member元素，不存在的member元素会被忽略
     * @param string $key
     * @param string ...$member
     * @return int|bool 返回成功移除的数量，如果返回false说明key不是Set类型
     */
    public function sRem(string $key, string ...$member)
    {
        return Redis::srem($key, ...$member);
    }

    /**
     * 返回集合key中的所有成员
     * @param string $key
     * @return array
     */
    public function sMembers(string $key): array
    {
        $res = Redis::smembers($key);
        return !$res ? [] : $res;
    }

    /**
     * 判断member元素是否是集合key的成员
     * @param string $key
     * @param string $member
     * @return bool
     */
    public function sIsMember(string $key, string $member): bool
    {
        return Redis::sismember($key, $member);
    }

    /**
     * 返回集合中元素的数量
     * @param string $key
     * @return int 没有该key护着key不是Set类型统一返回0
     */
    public function sCard(string $key): int
    {
        return (int)Redis::scard($key);
    }

    /**
     * 将member元素从{$sourceKey}集合移动到{$destinationKey}集合
     * @param string $sourceKey
     * @param string $destinationKey
     * @param string $member
     * @return bool
     */
    public function sMove(string $sourceKey, string $destinationKey, string $member): bool
    {
        return Redis::smove($sourceKey, $destinationKey, $member);
    }

    /**
     * 移除并返回集合中的一个随机元素
     * @param string $key
     * @return mixed
     */
    public function sPop(string $key)
    {
        #return Redis::spop($key);
        return Redis::command('spop', (array)$key);
    }

    /**
     * 返回集合中的一个或多个随机成员元素,返回元素的数量由{$count}决定
     * @param string $key
     * @param int $count 可为负数,当为负数的时候则返回个数是{$count}的绝对值，值可能会重复
     * @return mixed
     */
    public function sRandMember(string $key, int $count)
    {
        return Redis::srandmember($key, $count);
    }

    /**
     * 该集合是所有给定集合的交集
     * @param string ...$key
     * @return mixed
     */
    public function sInter(string ...$key)
    {
        return Redis::sinter($key);
    }

    /**
     * 该集合是所有给定集合的交集，结果保存到{$destinationKey}集合
     * @param string $destinationKey
     * @param string ...$key
     * @return int 返回结果集中的成员数量
     */
    public function sInterStore(string $destinationKey, string ...$key): int
    {
        return (int)Redis::sinterstore($destinationKey, ...$key);
    }

    /**
     * 返回给定集合的并集
     * @param string ...$key
     * @return mixed
     */
    public function sUnion(string ...$key)
    {
        return Redis::sunion($key);
    }

    /**
     * 返回给定集合的并集，结果保存到{$destinationKey}集合
     * @param string $destinationKey
     * @param mixed ...$key
     * @return int 返回结果集中的成员数量
     */
    public function sUnionStore(string $destinationKey, string ...$key): int
    {
        return (int)Redis::sunionstore($destinationKey, ...$key);
    }

    /**
     * 返回给定集合的差集
     * @param string ...$key
     * @return mixed
     */
    public function sDiff(string ...$key)
    {
        return Redis::sdiff($key);
    }

    /**
     * 返回给定集合的差集,结果保存到{$destinationKey}集合
     * @param string $destinationKey
     * @param string ...$key
     * @return int
     */
    public function sDiffStore(string $destinationKey, string ...$key): int
    {
        return (int)Redis::sdiffstore($destinationKey, ...$key);
    }

    /************** Sorted Set ***************/

}
