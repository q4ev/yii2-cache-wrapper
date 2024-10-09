<?php

namespace q4ev\caching;


use yii\caching\TagDependency;

/**
 * @mixin \yii\caching\Cache
 */
class CacheWrapper extends \yii\base\Component
{
	protected function __configureDependency ($dependency)
	{
		return null === $dependency || $dependency instanceof TagDependency
			? $dependency
			: new TagDependency([ 'tags' => (array)$dependency ]);
	}

	public function __call ($name, $params)
	{
		return \Yii::$app->cache->$name(...$params);
	}

	public function getOrSet ($key, $callable, $duration = null, $dependency = null, $noCache = false)
	{
		return $noCache || !\Yii::$app->has('cache')
			? call_user_func($callable)
			: \Yii::$app->cache->getOrSet(
				$key,
				$callable,
				$duration,
				$this->__configureDependency($dependency)
			);
	}

	public function reset ($tags = null)
	{
		if (!$tags)
			\Yii::$app->cache->flush();
		else
			TagDependency::invalidate(\Yii::$app->cache, $tags);
	}

	public function set ($key, $value, $duration = null, $dependency = null)
	{
		return \Yii::$app->cache->set(
			$key,
			$value,
			$duration,
			$this->__configureDependency($dependency)
		);
	}
}