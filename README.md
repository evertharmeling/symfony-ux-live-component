# Reproducing project for symfony/ux-live-component together with an embeddable doctrine object

## Get the project running
- run `composer install`
- run `yarn install --force`
- run `yarn dev`
- run `tools/.start-server`
- open https://127.0.0.1:8000/

## Error

`An exception has been thrown during the rendering of a template ("Warning: Undefined array key 0").`

When the lines `20-23` in `config/packages/doctrine.yaml` are commented, error does not occur.

## Cause

When the (in this case) `Money` object is 'managed' by Doctrine, it switches to the `DoctrineObjectNormalizer` when the
`\Symfony\UX\LiveComponent\LiveComponentHydrator::dehydrate()` function is called (specifically on line 88).
That results in a call of the method `\Doctrine\Persistence\Mapping\ClassMetadata::getIdentifierValues()` in 
`\Symfony\UX\LiveComponent\Normalizer\DoctrineObjectNormalizer::normalize` (on line 44).
But since the `Money` entity is an `embeddable` it does not have an 'identifier' and thus logically results in the error.

## Possible solution

- Respond with a proper error, update `\Symfony\UX\LiveComponent\Normalizer\DoctrineObjectNormalizer::normalize` function.

```
    public function normalize(mixed $object, string $format = null, array $context = []): mixed
    {
        $classMetaData = $this
            ->objectManagerFor($class = \get_class($object))
            ->getClassMetadata($class);

        if (\count($classMetaData->getIdentifier()) === 0) {
            throw new \RuntimeException("Cannot dehydrate an entity ({$class}) without an identifier. If you want to allow this, add a dehydrateWith= option to LiveProp.");
        }

        $id = $classMetaData->getIdentifierValues($object);

        switch (\count($id)) {
            case 0:
                throw new \RuntimeException("Cannot dehydrate an unpersisted entity ({$class}). If you want to allow this, add a dehydrateWith= option to LiveProp.");
            case 1:
                return array_values($id)[0];
        }

        // composite id
        return $id;
    }
```

- When this case occurs skip the `DoctrineObjectNormalizer` and fallback to the default normalizer?
