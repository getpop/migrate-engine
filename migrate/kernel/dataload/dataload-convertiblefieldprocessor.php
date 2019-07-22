<?php
namespace PoP\Engine;
use PoP\Engine\Facades\InstanceManagerFacade;

abstract class ConvertibleFieldValueResolverBase extends FieldValueResolverBase
{
    abstract protected function getDefaultFieldValueResolverClass();

    protected function getFieldValueResolverAndResolver($resultitem)
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        // Among all registered fieldvalueresolvers, check if any is able to process the object, through function `process`
        // Important: iterate from back to front, because more general components (eg: Users) are defined first,
        // and dependent components (eg: Communities, Organizations) are defined later
        // Then, more specific implementations (eg: Organizations) must be queried before more general ones (eg: Communities)
        // This is not a problem by making the corresponding field processors inherit from each other, so that the more specific object also handles
        // the fields for the more general ones (eg: FieldValueResolver_OrganizationUsers extends FieldValueResolver_CommunityUsers, and FieldValueResolver_CommunityUsers extends FieldValueResolver_Users)
        $resolver_manager = ConvertibleFieldValueResolverResolverManagerFactory::getInstance();
        $resolverClasses = array_reverse($resolver_manager->getRelationships(get_called_class()));
        foreach ($resolverClasses as $resolverClass) {

            $maybe_fieldvalueresolver_resolver = $instanceManager->getInstance($resolverClass);
            if ($maybe_fieldvalueresolver_resolver->process($resultitem)) {
                // Found it!
                $fieldvalueresolver_resolver = $maybe_fieldvalueresolver_resolver;
                $fieldValueResolverClass = $fieldvalueresolver_resolver->getFieldValueResolverClass();
                break;
            }
        }

        // If none found, use the default one
        $fieldValueResolverClass = $fieldValueResolverClass ?? $this->getDefaultFieldValueResolverClass();

        // From the fieldValueResolver name, return the object
        $fieldValueResolver = $instanceManager->getInstance($fieldValueResolverClass);

        // Return also the resolver, as to cast the object
        return array($fieldValueResolver, $fieldvalueresolver_resolver);
    }
    
    public function getValue($resultitem, $field)
    {

        // Delegate to the FieldValueResolver corresponding to this object
        list($fieldValueResolver, $fieldvalueresolver_resolver) = $this->getFieldValueResolverAndResolver($resultitem);

        // Cast object, eg from post to event
        if ($fieldvalueresolver_resolver) {
            $resultitem = $fieldvalueresolver_resolver->cast($resultitem);
        }

        // Delegate to that fieldValueResolver to obtain the value
        return $fieldValueResolver->getValue($resultitem, $field);
    }

    public function getFieldDefaultDataloaderClass($field)
    {

        // Please notice that we're getting the default dataloader from the default fieldValueResolver
        if ($defaultFieldValueResolverClass = $this->getDefaultFieldValueResolverClass()) {
            $instanceManager = InstanceManagerFacade::getInstance();
            $default_fieldvalueresolver = $instanceManager->getInstance($defaultFieldValueResolverClass);
            $default_dataloader = $default_fieldvalueresolver->getFieldDefaultDataloaderClass($field);
            if ($default_dataloader) {
                return $default_dataloader;
            }
        }

        return parent::getFieldDefaultDataloaderClass($field);
    }
}
