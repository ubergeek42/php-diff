<?php

namespace Pitpit\Diff;

/**
 * An object to compare two variable values.
 *
 * @author    Damien Pitard <damien.pitard@gmail.com>
 */
class Diff implements \Iterator, \ArrayAccess, \Countable
{
    const STATUS_UNCHANGED = 0;
    const STATUS_CREATED = 1;
    const STATUS_MODIFIED = 2;
    const STATUS_DELETED = 4;

    protected $status;
    protected $old;
    protected $new;
    protected $diffs = array();

    /**
     * Constructor
     *
     * @param mixed $old The old value
     * @param mixed $new The new value
     */
    public function __construct($old, $new)
    {
        $this->old = $old;
        $this->new = $new;
    }

    /**
     * Set the status of this Diff
     *
     * @param integer $status The status this Diff.
     *   Could be equal to Diff::STATUS_UNCHANGED, Diff::STATUS_CREATED, Diff::STATUS_MODIFIED or Diff::STATUS_DELETED
     *
     * @throws InvalidArgumentException If given status is not equals to one of the Diff::STATUS_ constant
     */
    public function setStatus($status)
    {
        if ($status !== self::STATUS_UNCHANGED && $status !== self::STATUS_CREATED && $status !== self::STATUS_MODIFIED && $status !== self::STATUS_DELETED) {
            throw new \InvalidArgumentException(sprintf('Invalid status value "%s"', $status));
        }

        $this->status = $status;
    }

    /**
     * @see Countable
     *
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function next()
    {
        return next($this->diffs);
    }

    /**
     * @see Iterator
     *
     * {@inheritdoc}
     */
    public function valid()
    {
        return (false !== current($this->diffs));
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Diff) {
            throw new \InvalidArgumentException("Not a Diff instance");
        }

        if (null === $offset) {
            $this->diffs[] = $value;
        } else {
            $this->diffs[$offset] = $value;
        }

        reset($this->diffs);
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->diffs[$offset]);
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->diffs[$offset]);
    }

    /**
     * @see ArrayAccess
     *
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return isset($this->diffs[$offset])?$this->diffs[$offset]:null;
    }

    /**
     * Dynamically add an object property to compare values of a property in the origin object.
     *
     * @param string $name The name of the compared property
     * @param Diff   $diff The Diff comparing the values of a property in the origin object
     */
    public function addProperty($name, Diff $diff)
    {
         $this->{$name} = $diff;
    }

    public function getSubdiff()
    {
        return $this->diff;
    }

    /**
     * Get the current value of the compared variables.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (self::STATUS_DELETED === $this->status) {
            return $this->old;
        } else {
            return $this->new;
        }
    }

    /**
     * Get the old value of the variable
     *
     * @return mixed|null Null if the variable has been created
     */
    public function getOld()
    {
        return $this->old;
    }

    /**
     * Get the new value of the variable
     *
     * @return mixed|null Null if the variable has been deleted
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Does the variable has been created
     *
     * @return boolean
     */
    public function getCreated()
    {
        return (self::STATUS_CREATED === $this->status);
    }

    /**
     * Does the variable has been deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return (self::STATUS_DELETED === $this->status);
    }

    /**
     * Is the variable modified
     *
     * @return boolean
     */
    public function getModified()
    {
        return (self::STATUS_MODIFIED === $this->status);
    }

    /**
     * Is the variable is changed
     *
     * @return boolean
     */
    public function getUnchanged()
    {
        return (self::STATUS_UNCHANGED === $this->status);
    }
}