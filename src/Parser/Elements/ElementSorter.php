<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

class ElementSorter implements ElementSorterInterface
{

    public function sortElementsByFqn(array $elements)
    {
        if (count($elements)) {
            $firstElement = array_values($elements)[0];
            if ($firstElement instanceof ConstantReflectionInterface) {
                return $this->sortConstantsByFqn($elements);
            } elseif ($firstElement instanceof FunctionReflectionInterface) {
                return $this->sortFunctionsByFqn($elements);
            } elseif ($firstElement instanceof InClassInterface) {
                return $this->sortPropertiesOrMethodsByFqn($elements);
            }
        }

        return $elements;
    }


    /**
     * @param ConstantReflectionInterface[] $constantReflections
     * @return ConstantReflectionInterface[]
     */
    private function sortConstantsByFqn($constantReflections)
    {
        usort($constantReflections, function ($a, $b) {
            return $this->compareConstantsByFqn($a, $b);
        });
        return $constantReflections;
    }


    /**
     * @param FunctionReflectionInterface[] $functionReflections
     * @return FunctionReflectionInterface[]
     */
    private function sortFunctionsByFqn($functionReflections)
    {
        usort($functionReflections, function ($a, $b) {
            return $this->compareFunctionsByFqn($a, $b);
        });
        return $functionReflections;
    }


    /**
     * @param InClassInterface[] $elementReflections
     * @return MethodReflectionInterface[]
     */
    private function sortPropertiesOrMethodsByFqn($elementReflections)
    {
        usort($elementReflections, function ($a, $b) {
            return $this->compareMethodsOrPropertiesByFqn($a, $b);
        });
        return $elementReflections;
    }


    private function compareConstantsByFqn(
        ConstantReflectionInterface $reflection1,
        ConstantReflectionInterface $reflection2
    ): int {
        return strcasecmp($this->getConstantFqnName($reflection1), $this->getConstantFqnName($reflection2));
    }


    private function getConstantFqnName(ConstantReflectionInterface $reflection): string
    {
        $class = $reflection->getDeclaringClassName() ?: $reflection->getNamespaceName();
        return $class . '\\' . $reflection->getName();
    }


    private function compareFunctionsByFqn(
        FunctionReflectionInterface $reflection1,
        FunctionReflectionInterface $reflection2
    ): int {
        return strcasecmp($this->getFunctionFqnName($reflection1), $this->getFunctionFqnName($reflection2));
    }


    private function getFunctionFqnName(FunctionReflectionInterface $reflection): string
    {
        return $reflection->getNamespaceName() . '\\' . $reflection->getName();
    }


    private function compareMethodsOrPropertiesByFqn(InClassInterface $reflection1, InClassInterface $reflection2): int
    {
        return strcasecmp(
            $this->getPropertyOrMethodFqnName($reflection1),
            $this->getPropertyOrMethodFqnName($reflection2)
        );
    }


    private function getPropertyOrMethodFqnName(InClassInterface $reflection): string
    {
        return $reflection->getDeclaringClassName() . '::' . $reflection->getName();
    }
}
