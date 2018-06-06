<?php
/**
 * CustomMinimalConverter
 *
 * @package php-logical-filter
 * @author  Jean Claveau
 */
namespace JClaveau\LogicalFilter\Converter;
use       JClaveau\LogicalFilter\Converter\ConverterInterface;
use       JClaveau\LogicalFilter\LogicalFilter;

/**
 * This class implements a converter for MySQL.
 */
class InlineSqlMinimalConverter extends MinimalConverter implements ConverterInterface
{
    /** @var array $output */
    protected $output = [];

    /**
     * @param LogicalFilter $filter
     */
    public function convert( LogicalFilter $filter )
    {
        $this->output = [];
        parent::convert($filter);
        return '('.implode(') OR (', $this->output).')';
    }

    /**
     */
    public function onOpenOr()
    {
        $this->output[] = [];
    }

    /**
     */
    public function onCloseOr()
    {
        $last_key = $this->getLastOrOperandKey();
        $this->output[ $last_key ] = implode(' AND ', $this->output[ $last_key ]);
    }

    /**
     * Pseudo-event called while for each And operand of the root Or.
     * These operands must be only atomic Rules.
     */
    public function onAndPossibility($field, $operator, $operand, array $allOperandsByField)
    {
        if ($operator == '=') {
            $new_rule = " $field = {$operand->getValue()} ";
        }
        elseif ($operator == '<') {
            $new_rule = " $field < {$operand->getMaximum()} ";
        }
        elseif ($operator == '>') {
            $new_rule = " $field > {$operand->getMinimum()} ";
        }

        $this->appendToLastOrOperandKey($new_rule);
    }

    /**
     */
    protected function getLastOrOperandKey()
    {
        end($this->output);
        return key($this->output);
    }

    /**
     * @param string $rule
     */
    protected function appendToLastOrOperandKey($rule)
    {
        $last_key = $this->getLastOrOperandKey();
        $this->output[ $last_key ][] = $rule;
    }

    /**/
}