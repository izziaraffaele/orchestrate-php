<?php
namespace andrefelipe\Orchestrate\Objects;

use andrefelipe\Orchestrate\Common\ObjectArray;
use andrefelipe\Orchestrate\Common\ObjectArrayTrait;
use andrefelipe\Orchestrate\Common\ToJsonInterface;
use JmesPath\Env as JmesPath;

abstract class AbstractObject extends AbstractResponse implements
\ArrayAccess,
\Countable,
ValueInterface,
ToJsonInterface
{
    use ObjectArrayTrait;

    public function __get($key)
    {
        return isset($this->{$key}) ? $this->{$key} : null;
    }

    public function __set($key, $value)
    {
        // testing this..
        // would need a 'addProperty' method that stores a PropertyMap

        // $possibleSetter = 'set'.ucfirst($key);
        // if (method_exists($this, $possibleSetter)) {
        //     $this->{$possibleSetter}($value);
        // } else

        if (is_array($value)) {
            $this->{$key} = new ObjectArray($value);
        } else {
            $this->{$key} = $value;
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset) || is_int($offset)) {
            $this->noIndexedArrayException();
        }

        $this->{(string) $offset} = $value;
    }

    public function getValue()
    {
        return (new ObjectArray())->merge($this);
    }

    public function extractValue($expression)
    {
        $result = JmesPath::search($expression, $this->getValue()->toArray());
        return is_array($result) ? new ObjectArray($result) : $result;
    }

    public function setValue(array $values)
    {
        if ($values) {
            foreach ($values as $key => $value) {

                if (is_int($key)) {
                    $this->noIndexedArrayException();
                }
                $this->{(string) $key} = $value;
            }
        }
        return $this;
    }

    public function mergeValue($object)
    {
        if (is_object($object)) {

            $this->_mergeObject($object);

        } else if (is_array($object)) {

            foreach ($object as $key => $value) {

                if (is_int($key)) {
                    $this->noIndexedArrayException();
                }

                $key = (string) $key;
                if (is_array($value)) {
                    $value = new ObjectArray($value);
                }

                if (isset($this->{$key}) && is_object($value) && is_object($this->{$key})) {
                    $this->_mergeObject($value, $this->{$key});
                } else {
                    $this->{$key} = $value;
                }
            }
        }
        return $this;
    }

    public function resetValue()
    {
        $properties = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC);
        // strictly gets the public properties, otherwise we would be getting
        // all properties accessible on this scope (i.e. protected and privates)

        foreach ($properties as $property) {
            $this->{$property->name} = null;
        }
        return $this;
    }

    private function noIndexedArrayException()
    {
        throw new \RuntimeException('Indexed arrays not allowed at the root of ' . get_class($this) . ' objects.');
    }
}
