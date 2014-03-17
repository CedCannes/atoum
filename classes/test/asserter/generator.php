<?php

namespace mageekguy\atoum\test\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

class generator extends asserter\generator
{
	protected $test = null;

	public function __construct(atoum\test $test, asserter\resolver $resolver = null, assertion\aliaser $aliaser = null)
	{
		parent::__construct($test->getLocale(), $resolver, $aliaser);

		$this->test = $test;
	}

	public function __get($property)
	{
		return $this->test->__get($property);
	}

	public function __call($method, $arguments)
	{
		return $this->test->__call($method, $arguments);
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this->setLocale($test->getLocale());
	}

	public function getTest()
	{
		return $this->test;
	}

	public function asserterPass(atoum\asserter $asserter)
	{
		$this->test->getScore()->addPass();

		return $this;
	}

	public function asserterFail(atoum\asserter $asserter, $reason)
	{
		$class = $this->test->getClass();
		$method = $this->test->getCurrentMethod();
		$file = $this->test->getPath();
		$line = null;
		$function = null;

		foreach (array_filter(debug_backtrace(false), function($backtrace) use ($file) { return isset($backtrace['file']) === true && $backtrace['file'] === $file; }) as $backtrace)
		{
			if ($line === null && isset($backtrace['line']) === true)
			{
				$line = $backtrace['line'];
			}

			if ($function === null && isset($backtrace['object']) === true && isset($backtrace['function']) === true && $backtrace['object'] === $asserter && $backtrace['function'] !== '__call')
			{
				$function = $backtrace['function'];
			}
		}

		throw new asserter\exception($reason, $this->test->getScore()->addFail($file, $class, $method, $line, get_class($asserter) . ($function ? '::' . $function : '') . '()', $reason));
	}

	public function getAsserterInstance($asserter, array $arguments = array(), atoum\test $test = null)
	{
		return parent::getAsserterInstance($asserter, $arguments, $test ?: $this->test);
	}
}
