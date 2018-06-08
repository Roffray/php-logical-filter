<?php
/**
 * InRule
 *
 * @package php-logical-filter
 * @author  Jean Claveau
 */
namespace JClaveau\LogicalFilter\Rule;

/**
 * This class represents a rule that expect a value to belong to a list of others.
 */
class InRule extends OrRule
{
    /** @var string operator */
    const operator = 'in';

    /**
     * @param string $field         The field to apply the rule on.
     * @param array  $possibilities The values the field can belong to.
     */
    public function __construct( $field, array $possibilities )
    {
        $this->field = $field;
        $this->addPossibilities( $possibilities );
    }

    /**
     * @return array
     */
    public function getPossibilities()
    {
        $possibilities = [];
        foreach ($this->operands as $operand)
            $possibilities[] = $operand->getValue();

        return $possibilities;
    }

    /**
     * @param  array possibilities
     *
     * @return InRule $this
     */
    public function addPossibilities(array $possibilities)
    {
        foreach ($possibilities as $possibility) {
            if ($possibility instanceof AbstractRule) {
                throw new \InvalidArgumentException(
                    "A possibility cannot be a rule: "
                    . var_export($possibility, true)
                );
            }

            $this->operands[] = new EqualRule($this->field, $possibility);
        }

        return $this;
    }

    /**
     * @param bool $debug=false
     */
    public function toArray($debug=false)
    {
        $description = [
            $this->field,
            $debug ? $this->getInstanceId() : self::operator,
            $this->getPossibilities()
        ];

        return $description;
    }

    /**/
}
