<?php

namespace App\Controller\Api;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait SerializesEntitiesToJson
{
    public function serializeEntityToJsonResponse($entities, $key = 0, $groups = null)
    {
        ////TODO find a way to disable fetching relationships for nested objects
        /// groups unfortunately cannot handle nested descriptions
        /// perhaps implementing JsonSerializable with determining the call stack level?

        $encoder = new JsonEncoder();
        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return new \stdClass(); //// TODO replace with routing link
            },
        ];
        $normalizer = new ObjectNormalizer(
            $classMetadataFactory,
            null,
            null,
            null,
            null,
            null,
            $defaultContext
        );

        $serializer = new Serializer([$normalizer], [$encoder]);

        $data = [];
        if (!empty($key) and is_countable($entities) and count($entities) > 1) {
            $data = $entities;
        } else {
            $data[$key] = $entities;
        }

        return new JsonResponse(
            $serializer->serialize(
                [
                    'code' => 0,
                    'data' => $data
                ],
                'json',
                [
                    AbstractObjectNormalizer::GROUPS => $groups,
                    AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                    AbstractObjectNormalizer::IGNORED_ATTRIBUTES => [
                        'deleted',
                        '__initializer__',
                        '__cloner__',
                        '__isInitialized__'
                    ]
                ]
            ),
            200, [], true
        );
    }
}