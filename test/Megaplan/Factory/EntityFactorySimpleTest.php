<?php


namespace Megaplan\Factory;


use PHPUnit\Framework\TestCase;
use rollun\api\megaplan\Entity\EntitySimple;
use rollun\api\megaplan\Factory\EntityFactorySimple;

class EntityFactorySimpleTest extends TestCase
{
    public function testCreateSimpleEntity()
    {
        $factory = new EntityFactorySimple();
        $data = $this->getTestData();
        $entity = $factory->createConcrete($data);

        $this->assertInstanceOf(EntitySimple::class, $entity);
        $this->assertEquals(10488, $entity->get('Id'));
        $this->assertEquals('Amazon Shoptimistic', $entity['MpName']);
    }

    protected function getTestData()
    {
        return json_decode('
            {
                "class": "BumsTradeM_Deal",
                "id": 10488,
                "data": {
                    "deal": {
                        "Id": "10488",
                        "GUID": "",
                        "Name": "\u21164799",
                        "Contractor": {
                            "Id": 1004518,
                            "Name": "Jacob Butts"
                        },
                        "TimeCreated": "2020-03-23 13:55:05",
                        "TimeUpdated": "2020-03-24 16:27:48",
                        "Owner": {
                            "Id": 1000007,
                            "Name": "Yuliia Sarsania"
                        },
                        "IsDraft": false,
                        "Positions": [
                            {
                                "Id": "123638",
                                "Name": "TUSK  TPCP16  21755947105218",
                                "Count": "1",
                                "DeclaredPrice": {
                                    "Value": 25.98,
                                    "Currency": "$",
                                    "CurrencyId": 2,
                                    "CurrencyAbbreviation": "USD",
                                    "Rate": 1
                                },
                                "DiscountType": "0",
                                "DiscountValue": "",
                                "Cost": {
                                    "Value": 25.98,
                                    "Currency": "$",
                                    "CurrencyId": 2,
                                    "CurrencyAbbreviation": "USD",
                                    "Rate": 1
                                },
                                "Offer": {
                                    "Id": 174155,
                                    "Tax": {
                                        "Id": 1,
                                        "Name": "\u041f\u0414\u0412"
                                    },
                                    "Unit": {
                                        "Id": 1,
                                        "Name": "pcs."
                                    },
                                    "OriginOffer": {
                                        "Id": 13713,
                                        "Name": "TUSK  TPCP16"
                                    },
                                    "Name": "TUSK  TPCP16  21755947105218"
                                }
                            },
                            {
                                "Id": "123639",
                                "Name": "ROLLUN  SHIPPING",
                                "Count": "1",
                                "DeclaredPrice": {
                                    "Value": 0,
                                    "Currency": "$",
                                    "CurrencyId": 2,
                                    "CurrencyAbbreviation": "USD",
                                    "Rate": 1
                                },
                                "DiscountType": "0",
                                "DiscountValue": "",
                                "Cost": {
                                    "Value": 0,
                                    "Currency": "$",
                                    "CurrencyId": 2,
                                    "CurrencyAbbreviation": "USD",
                                    "Rate": 1
                                },
                                "Offer": {
                                    "Id": 174156,
                                    "Tax": {
                                        "Id": 1,
                                        "Name": "\u041f\u0414\u0412"
                                    },
                                    "Unit": {
                                        "Id": 1,
                                        "Name": "pcs."
                                    },
                                    "OriginOffer": {
                                        "Id": 50232,
                                        "Name": "ROLLUN  SHIPPING"
                                    },
                                    "Name": "ROLLUN  SHIPPING"
                                }
                            }
                        ],
                        "IsPaid": false,
                        "FinalPrice": {
                            "Value": 25.98,
                            "Currency": "$",
                            "CurrencyId": 2,
                            "CurrencyAbbreviation": "USD",
                            "Rate": 1
                        },
                        "Program": {
                            "Id": 25,
                            "Name": "Order"
                        },
                        "RelatedObjects": [
                            {
                                "Id": 10538,
                                "Name": "\u21163222",
                                "Type": "deal"
                            }
                        ],
                        "Category1000072CustomFieldMpName": "Amazon Shoptimistic",
                        "Category1000072CustomFieldMpClientId": "",
                        "Category1000072CustomFieldMpOrderNumber": "112-2313403-5098649",
                        "Category1000072CustomFieldMpShipMethod": "Standard",
                        "Category1000072CustomFieldMpOrderNote": "",
                        "Category1000072CustomFieldMpOrderItemId": "21755947105218",
                        "Category1000072CustomFieldDateCrPayed": "2020-03-23 14:19:12",
                        "Category1000072CustomFieldDateShipBy": "2020-03-26 06:59:59",
                        "Category1000072CustomFieldDateDeliverBy": "2020-04-01 06:59:59",
                        "Category1000072CustomFieldTracknumber": "yes",
                        "Category1000072CustomFieldProblemDescription": "-",
                        "Auditors": [],
                        "Manager": {
                            "Id": 1000007,
                            "Name": "Yuliia Sarsania"
                        },
                        "Status": {
                            "Id": 172,
                            "Name": "60 day test"
                        },
                        "ProgramId": 25,
                        "PossibleTransitions": [
                            {
                                "Id": "trans-271",
                                "Name": "Archive",
                                "Comment": "Transition disabled because NOT problem description not equal -",
                                "Disabled": true,
                                "Destination": {
                                    "Id": 173,
                                    "Name": "Archive",
                                    "EntryPointName": "Archive",
                                    "Type": "positive",
                                    "Color": "#e3db47",
                                    "Description": "",
                                    "IsEntry": false
                                }
                            }
                        ]
                    }
                }
            }
        ', true);
    }
}