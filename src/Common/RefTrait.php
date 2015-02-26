<?php
namespace andrefelipe\Orchestrate\Common;

/**
 * Trait that implements the Ref methods.
 * 
 * @internal
 */
trait RefTrait
{
    /**
     * @var string
     */
    private $_ref = null;

    /**
     * @return string
     */
    public function getRef($required = false)
    {
        if ($required)
            $this->noRefException();
        
        return $this->_ref;
    }

    /**
     * @param string $ref
     */
    public function setRef($ref)
    {
        $this->_ref = (string) $ref;

        return $this;
    }

    protected function setRefFromETag()
    {
        if ($etag = $this->response->getHeader('ETag')) {
            $this->_ref = trim($etag, '"');
        }
    }

    /**
     * @throws \BadMethodCallException if 'ref' is not set yet.
     */
    protected function noRefException()
    {
        if (!$this->_ref) {
            throw new \BadMethodCallException('There is no ref set yet. Please do so through setRef() method.');
        }
    }
}
