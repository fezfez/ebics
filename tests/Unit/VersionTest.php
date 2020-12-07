<?php

declare(strict_types=1);

namespace Fezfez\Ebics\Tests\Unit;

use Fezfez\Ebics\Crypt\BankPublicKeyDigest;
use Fezfez\Ebics\KeyRing;
use Fezfez\Ebics\Version;
use PHPUnit\Framework\TestCase;

use function json_decode;

class VersionTest extends TestCase
{
    public function testVersionConstruct(): void
    {
        self::assertSame('H003', Version::v24()->value());
        self::assertSame('H004', Version::v25()->value());
        self::assertSame('H005', Version::v30()->value());
        self::assertTrue(Version::v24()->is(Version::v24()));
        self::assertTrue(Version::v25()->is(Version::v25()));
        self::assertTrue(Version::v30()->is(Version::v30()));
        self::assertFalse(Version::v30()->is(Version::v24()));
    }

    public function testTototo(): void
    {
        $tmp = KeyRing::fromArray(
            json_decode(
                '{"bankCertificateE":{"type":"E","public":"LS0tLS1CRUdJTiBSU0EgUFVCTElDIEtFWS0tLS0tDQpNSUlCQ2dLQ0FRRUFnY0lWQ0wraFVKS2VNVlpNbHUwdUZHckFjdzhHN2NpRm43VktOWWVwMW8xOGIvR21NL0t3DQpmWk9nV3Y3UnJBejZaTzdZVEREc1k2N0NFcHcwenhkVloyUDhkaEZRb0RIK3VDSnI2MDQ1Rko4QlIzeDVuNUg2DQo5OURIMUo1OWlPQWVTSnBTQnVzSHA4enpCalFYc2tPREU5eVBvZ2NWWFV6azAyWWhPdWttU3REbEZHTzVRSlNCDQpXeHJzRGJSZ2dMK3BIcFZXc1IzcVU3dlZVZjJSMU5EcnRLOXRCSmo5dUxVRnFFd05FWkR3U3V0RDlSTGpqMGsxDQo5MktiRDhlbHBJVnBWZDJRa1lEY2dWbi9LRGRmdHJaL3JVZDd5SHU0UU80akxOMW1uRzI1NVc3WHRCQXpVN1dyDQp3RkJnMzloVmZBOXpiSFRzd0ZQYzMxZDJHb0JZZVd0OEp3SURBUUFCDQotLS0tLUVORCBSU0EgUFVCTElDIEtFWS0tLS0t","content":"MzA4MjAzMjEzMDgyMDIwOWEwMDMwMjAxMDIwMjA0MGQ3NDFmNTQzMDBkMDYwOTJhODY0ODg2ZjcwZDAxMDEwYjA1MDAzMDMyMzExYTMwMTgwNjAzNTUwNDAzMGMxMTQzNTI0MzQxMjA0MzY1NmU3NDcyNjUyMDQ1NzM3NGMyYTAzMTE0MzAxMjA2MDM1NTA0MGEwYzBiNDE0NzUyNDk0NjUyNTA1MDM4MzczODMwMWUxNzBkMzEzOTMxMzIzMDM4MzIzMzMwMzAzMDMwNWExNzBkMzIzNDMxMzIzMDM4MzIzMzMwMzAzMDMwNWEzMDMyMzExYTMwMTgwNjAzNTUwNDAzMGMxMTQzNTI0MzQxMjA0MzY1NmU3NDcyNjUyMDQ1NzM3NGMyYTAzMTE0MzAxMjA2MDM1NTA0MGEwYzBiNDE0NzUyNDk0NjUyNTA1MDM4MzczODMwODIwMTIyMzAwZDA2MDkyYTg2NDg4NmY3MGQwMTAxMDEwNTAwMDM4MjAxMGYwMDMwODIwMTBhMDI4MjAxMDEwMDgxYzIxNTA4YmZhMTUwOTI5ZTMxNTY0Yzk2ZWQyZTE0NmFjMDczMGYwNmVkYzg4NTlmYjU0YTM1ODdhOWQ2OGQ3YzZmZjFhNjMzZjJiMDdkOTNhMDVhZmVkMWFjMGNmYTY0ZWVkODRjMzBlYzYzYWVjMjEyOWMzNGNmMTc1NTY3NjNmYzc2MTE1MGEwMzFmZWI4MjI2YmViNGUzOTE0OWYwMTQ3N2M3OTlmOTFmYWY3ZDBjN2Q0OWU3ZDg4ZTAxZTQ4OWE1MjA2ZWIwN2E3Y2NmMzA2MzQxN2IyNDM4MzEzZGM4ZmEyMDcxNTVkNGNlNGQzNjYyMTNhZTkyNjRhZDBlNTE0NjNiOTQwOTQ4MTViMWFlYzBkYjQ2MDgwYmZhOTFlOTU1NmIxMWRlYTUzYmJkNTUxZmQ5MWQ0ZDBlYmI0YWY2ZDA0OThmZGI4YjUwNWE4NGMwZDExOTBmMDRhZWI0M2Y1MTJlMzhmNDkzNWY3NjI5YjBmYzdhNWE0ODU2OTU1ZGQ5MDkxODBkYzgxNTlmZjI4Mzc1ZmI2YjY3ZmFkNDc3YmM4N2JiODQwZWUyMzJjZGQ2NjljNmRiOWU1NmVkN2I0MTAzMzUzYjVhYmMwNTA2MGRmZDg1NTdjMGY3MzZjNzRlY2MwNTNkY2RmNTc3NjFhODA1ODc5NmI3YzI3MDIwMzAxMDAwMWEzM2YzMDNkMzAwYzA2MDM1NTFkMTMwMTAxZmYwNDAyMzAwMDMwMGUwNjAzNTUxZDBmMDEwMWZmMDQwNDAzMDIwNWEwMzAxZDA2MDM1NTFkMGUwNDE2MDQxNGI3MjI4YjRlMDRlMzcyOTFkY2ZiYWQwNzIxZjYxMTc5OWRkYWFkMmYzMDBkMDYwOTJhODY0ODg2ZjcwZDAxMDEwYjA1MDAwMzgyMDEwMTAwMTg1ODY3YjJkNDYyZWJhYmU0NTY3ZGI1NGVjY2FhOGU2YWU0NjVhMDMzMzU2YzQ5YTc4YjkzODI0MGJmMzQyMDFiYTVkZWI0M2UyNTVjM2RjNDg4ZWIwODk4MDI5Zjk5ZmY3MjRlOGI2MGZhN2I1MDAyODcwNjRmMzIzNWE3NzIwZmIxNDI3ODRiNjRjZjQ3MzlkY2U3NWE2YmI0Mzc3MDYzZTMyN2Y5NGM2NmE1MjQwOTk4YjljYjA2Yzk3NTE3ZjEwMWYzOTMzMmNjZTc1MmY5YWJkMWI4N2YzM2VlN2Q4ZGQ1ZTQ0YzczOTMzYTM1ZDNkZmZiMTAwOWEyNjZiMDUzNjVkNTZiNGZiOTkxZjIxNWNjYzIzOWEzNzFlY2VjZGE3Y2FjZDUxZTM2YTEwNTIxZjRjYjllNzQ1MDljY2Y1MTNhNzNhN2JlOTllOThhZTEwYjUwY2ZlZDUxNGJmYzEzYjQ0YTgzNDNjYzEwMWY3Y2YxNjZmMDQzMGY1M2M4YzEzMmYyNTRlNWY3MTgwOGI0MWIxNjJhODIzM2ZiZmYwNmE4NzRjMmUyOWY5ZDMyOGJiNmJjZjU0NzNhM2FjOGYwMmVlNjk4YjdjODE3NWY1NWQ3ZmMyMjFjZDAzZGQwNTMxMmU4Y2QxNTQ1ODg2ZjYxZmRjYjMyNzI2M2I4ZDE="},"bankCertificateX":{"type":"X","public":"LS0tLS1CRUdJTiBSU0EgUFVCTElDIEtFWS0tLS0tDQpNSUlCQ2dLQ0FRRUF2R2NBbUxCZk95dTJrdUxjL3doc0xZa25mWHNqZDF0Ti9LVWhKRmxRTGdyWUZSb0VBMVZNDQo2ZDY2NnFiUlYrSEdnaEFYU1VEc1RUK3lZeWpCY01SS3ljaDFNMU9qanpSRllHaldDVTFrWnRVTG9VN0tQSWNSDQpzWnpKVlh1cDY5Yms1amw2cWtQYzI1UElVYVAyU3VyZno3a1krc0VMMXpucEE0K0pvSU1Da2NLSm1OUjh5Y0QwDQp1RFE4cVBVaVBlZHBrR2dkVFBLZGR3dStBMjRBODlaclNraEhsWTVvN1kxQllCT2dqTU5udklEdk0rak5aa3Z4DQpJNG41blI2UFhWU3c1SjFsV3B5ajhSc1E5VXY0dFJZbGV0dklZR3M3eUQ2OXhYZzRLRGpMV3ladDhneVFIUmQ5DQpob1pWWUpWYk9PWWVQWkN4bklheDRRS29lOUNyS3Y2cEZ3SURBUUFCDQotLS0tLUVORCBSU0EgUFVCTElDIEtFWS0tLS0t","content":"MzA4MjAzMjEzMDgyMDIwOWEwMDMwMjAxMDIwMjA0MzM5MGZjMjkzMDBkMDYwOTJhODY0ODg2ZjcwZDAxMDEwYjA1MDAzMDMyMzExYTMwMTgwNjAzNTUwNDAzMGMxMTQzNTI0MzQxMjA0MzY1NmU3NDcyNjUyMDQ1NzM3NGMyYTAzMTE0MzAxMjA2MDM1NTA0MGEwYzBiNDE0NzUyNDk0NjUyNTA1MDM4MzczODMwMWUxNzBkMzEzOTMxMzIzMDM4MzIzMzMwMzAzMDMwNWExNzBkMzIzNDMxMzIzMDM4MzIzMzMwMzAzMDMwNWEzMDMyMzExYTMwMTgwNjAzNTUwNDAzMGMxMTQzNTI0MzQxMjA0MzY1NmU3NDcyNjUyMDQ1NzM3NGMyYTAzMTE0MzAxMjA2MDM1NTA0MGEwYzBiNDE0NzUyNDk0NjUyNTA1MDM4MzczODMwODIwMTIyMzAwZDA2MDkyYTg2NDg4NmY3MGQwMTAxMDEwNTAwMDM4MjAxMGYwMDMwODIwMTBhMDI4MjAxMDEwMGJjNjcwMDk4YjA1ZjNiMmJiNjkyZTJkY2ZmMDg2YzJkODkyNzdkN2IyMzc3NWI0ZGZjYTUyMTI0NTk1MDJlMGFkODE1MWEwNDAzNTU0Y2U5ZGViYWVhYTZkMTU3ZTFjNjgyMTAxNzQ5NDBlYzRkM2ZiMjYzMjhjMTcwYzQ0YWM5Yzg3NTMzNTNhMzhmMzQ0NTYwNjhkNjA5NGQ2NDY2ZDUwYmExNGVjYTNjODcxMWIxOWNjOTU1N2JhOWViZDZlNGU2Mzk3YWFhNDNkY2RiOTNjODUxYTNmNjRhZWFkZmNmYjkxOGZhYzEwYmQ3MzllOTAzOGY4OWEwODMwMjkxYzI4OTk4ZDQ3Y2M5YzBmNGI4MzQzY2E4ZjUyMjNkZTc2OTkwNjgxZDRjZjI5ZDc3MGJiZTAzNmUwMGYzZDY2YjRhNDg0Nzk1OGU2OGVkOGQ0MTYwMTNhMDhjYzM2N2JjODBlZjMzZThjZDY2NGJmMTIzODlmOTlkMWU4ZjVkNTRiMGU0OWQ2NTVhOWNhM2YxMWIxMGY1NGJmOGI1MTYyNTdhZGJjODYwNmIzYmM4M2ViZGM1NzgzODI4MzhjYjViMjY2ZGYyMGM5MDFkMTc3ZDg2ODY1NTYwOTU1YjM4ZTYxZTNkOTBiMTljODZiMWUxMDJhODdiZDBhYjJhZmVhOTE3MDIwMzAxMDAwMWEzM2YzMDNkMzAwYzA2MDM1NTFkMTMwMTAxZmYwNDAyMzAwMDMwMGUwNjAzNTUxZDBmMDEwMWZmMDQwNDAzMDIwNWEwMzAxZDA2MDM1NTFkMGUwNDE2MDQxNDJmMGI1MjYxMDMxZjk0YWI0NGRiZTA1ZmNkMzg2NjQ4MTFlMzcwZGMzMDBkMDYwOTJhODY0ODg2ZjcwZDAxMDEwYjA1MDAwMzgyMDEwMTAwNmIyMjFlYzc2ZDYwNzg5ZDU1YzRmMWNiMjVhZTg3NGExODI5Njc2MjBlYWMyMzVlOTkzNTFjNWFkYTcxMWMzM2FlMGEzNjI1OTZjMTk3ZmI0MjMyYWJkYjBkYWJjZTQzN2RjMWY0Yjc5NzViMDRmYjliYzA0ZGMzMzMxNTMyMGEwMjU2Y2UwNTUxMjEwODI5YzU3NDc2NzMxOTIwZGFlYzZmMTE5NTQ4MjNlZTA1ZjY0NzRjMzVlZDhjYTI2ZmQxMTI1MGVhZGEyOGExZjk5MTI2YjYxMzg1M2FhNGFmYjQwMzgwNmI4MDEyMDQ4NjhjN2NjOGJjNjczNmFhMjA3MzQwMTU1OWEyYmY1NTRmYmJkNGFhMzA5ZTNmYzJhZjdmY2NlNWVlZDhjYWZhZTQwOTQ2YThiNWVjYzk5YmE4OTE3ZWQyODE5ZjI4MzllZDM5ZTA5OTMzYWU5YmNkNjA4OTllNGU0MDJjMDBlM2Y1YzAwNTIyNTAyYTRjMjQwZTY4NmZiODAwNzZjOGRjYmY2OTg3N2I0Njg2ZDBiMjk4ZTA0YmI1OTExOTMxMTE4ZGMwNWMwYzE1NDk1ZmViZDAwYTYyNmEwMmY3ZWE5YzEyNWRhZWFhYTcyYjg5MmVjMDJhMGU4ZjcyMDUyNTI2MDYxZmRhMmZjZTdjNjJmNmQ1NDE="},"userCertificateA":{"type":"A","public":"LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0NCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBellSTHIreHZ2OHBtRVNTaVN2bjENCno2R1ZURmovUGc1UVd2MUdQWVN5b1dHZTZBczJYazBJR2s1aXoxRDVvcVZ0UG9meWg5cWFXbk5TL0NVdGV6VGMNCkQ3cWxpNnQyMzl0dUgvSzc4dXN6aHVvSjBjWVhhWlNaY2FjdzBJeC9rMFpiUVdWSG9PbFNKQzhhTnNsZ0dpRzkNCk8vK3AvRmZ1NVZmL0plMmlScDh5UXExRUtyL3pHcTRyYTlESzVzMVBHTFM5L0ZEK2wwWDdsZjJzMkdPb2REbjkNCi9panl2bTJWTllmQlVBSmwxWElreHA1QUZPc2hEdjFFbHZqbG5VbzZ4R0FRVytRNWpNTFg1QXh3VmNnR3N6a1UNCkRwVE12ZUpwNDBvWTBOYnZZYlc1c083d0xrNFpDR0RLeDNjbkRmdU5tK2RhRTg2UTdSYVk5QXlTb2crNmtwNWMNCnJRSURBUUFCDQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0=","private":"LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQ0KUHJvYy1UeXBlOiA0LEVOQ1JZUFRFRA0KREVLLUluZm86IERFUy1FREUzLUNCQyw0MjExNkUzREY5MTRGMDdDDQoNClhSK1Q2eWVFYlY4UkVvY0RpNFhVWlNtbm1GQ2VzQ3NoU3I1SjJWN3BCTm02U1dPKzM4NHpiMXNUOTQ1OFAxTWcNCjFjRHlkWEFPWDJVMnVCOGFYQTZuN3hLQ2VoTkk4b1ZHbHBFR1RneTI3bEhrRFM0QXVwT0gwK0FtMVZSeGFNdFoNClB4eUd2OUVyKzF6V09GZnlUZkwzd1J6ci9vL0RicThQb2tRTkV4NUhvTjBrS3VCNXJSUEJLYnRQUUVpMUVpMkcNCkdPM1BjdXpiZG9ueS9sY3QzdFFlb2hhZnZPazhEQlY1MExhV3o2b1hPeXM2RVVEcTdDZTRkTU1VdzNjU2p2dWENClBtNXRVOGs0UTc5RHV4bktYWHhjZlRDYndNYXJTTnRhekJuZ1M4S1NzZlNJUWRrYVVZNHlUWnRPWk8zUlVyTUcNClZadDdaTXJqQkZnbmlKT0VnbG9yK3IzVHhLTHlUOW5HVkdXWExxN1kxYUdoSHdJODB4bzFXRW9tb3JXRFVaTUkNClppY0ticEFqd01wMGxSdnBmS1BMOVQ2bGZMZkFxUVpkWGFoaUdrcS80RXlrVW5EMWxvcTJmS3c0cTJkV3BKWnkNCmJDemMwYkIvYmVyM0lUNWk3dElsNVh1K2VicmpEbGpJK1RyeGw3aE90enZEVVZyZnhTa2J3ZkUyc0ZiaERQRHENCk4zcWc1OWJtbFUvVmNrbkdRZG00S3B3d1RvVkV0QUpFVzBrSzdSdTc0Y2hkT0VVLzd5K2lldVdFMHMrbFprclMNCmdiZDJINmdqOTJhd2tJSXdjWk9DWlh4bGM0RUhncytvck90bGNYeE9EclhrQTl4YkNyNldFVmlNL2U3NXd3clUNCkFRU0RFVGxDMmJQVUhEbFdBSjZ4NnE4bnhUYnNKZEd1elRvci9yalE2Q2NlSVBRQk1MbHJONjk1R29WQzYxL2oNCkxQRlkvS0NPQTZEems4WGI2NXJqMEtRaFBwRG1wdXZoNVRIdk0yTFBqT0xsVEk5Y29mY2VzVDVnUVBHaGdQODUNCnhTcFlueE81RGh0VnpoSTdXdGJJeFV6YVhJVlVjcFh2ZWNPUENMcjFaQ21pTG9oNXBLM05JcGVzOGdlVVh6dUcNCmZORTZvT3ZwazNOaTR0L1FHQTdYZTNqK01IZFEwWE9TQlFhVlNCS3VOc0xEVmcxeXlZbFJKZ2F6Tk9OVnZhcCsNClNWNnMzSHVCdURITExpY2o0cDAxQU9wY0JZRnJ2cEM0TDA0RHNJeW44R01OSlhSMEE5SDFWS0lrZTdGYXRzQVQNCmFYQ2hoMzVrdDkyUDdpT0FJRFAxR1cwM1lCRHlSSUNhVHROMjlUY2o4a3VVZEpnWk9TU05hN0x2VHVaMXZwTW4NCnNWU2oxYUp6Y1FpUXEvRVlRMUNPRjQ0TjFRUlcrdmxUVlFhT2VxSmNMeVZxWld3ZGtwMFordm1jSytuT1JKbnMNCldSUzBsWHpKdTJ5TXNVTEY4eVQzTHRodk5POFEyZkZ0VDNKOWwrUUFpQStCZ1lMQjNhaGNCNU4wemIzdE9jZ0INCkhaYXoxSVFGY2JPRTlNdDdJM0dHaC81SXkyQWZLSTY0b1RYVE9YcVpLcGc5eW83MDJNNENFL1ZTcHpWeWt4VUMNCld1bmlqYmM3THJQSkQ1OWpoclBYMWFlSDNzUDlWbWFxQjBvckRGTE04dytTNUt3QngvUzg3ZmNPdkNYSkdDVjYNCm12cUg1clBFOWZXdUhFSkJ4M2E2YW1rODloN0d1VkppWmIwemx2bldNdlVVVG9sU1BGRWwyTkp0ZjVXQm9WY1cNCklaR2xnZnVzTFVuSUFXS0NRZmFvUVo3MG42OGwwSVRTUXhMQXhuOThMSzdzNnFTUGMzOVR5QlhuMVdaV05XRHENClhJdGh0VUdiNzI2d0xjSU4yK3RhRExZbWh0NTdNaFFidjBBZjh0SFB4cVIzNFJrZFcxU0ZNOExldWpJd01Fa04NCkoySVdFK3pzdENVbWtBVzEvRnF4aTIvTUhPOEZmaVFUTk0yZm95TWp6Sm0xT0JIWHZPMFpjTmIvdHczakpQdlUNCk8rY0RTVUdzYVVIODZKc3JHWXBmSWtFZkl4bnJjdEtNZFM5STFTOGJzVmJFb05WTENFSC90QT09DQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQ==","content":"LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tDQpNSUlEMlRDQ0FzR2dBd0lCQWdJZ0FYTlB5dHlpdHh1ZlIvVWlJVXJjMGtpTmpSRlJPd21NNUFrRThCM2p5QUl3DQpEUVlKS29aSWh2Y05BUUVMQlFBd1dURUxNQWtHQTFVRUJnd0NSbEl4RkRBU0JnTlZCQWdNQzFKb2IyNWxMV0ZzDQpjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3RGdZRFZRUUtEQWRrYjI1bGRIUnBNUk13RVFZRFZRUUREQXBrDQpiMjVsZEhScExtWnlNQjRYRFRJd01UQXhOVEV3TkRZME5Wb1hEVEl6TVRBeE5qRXdORFkwTlZvd1dURUxNQWtHDQpBMVVFQmd3Q1JsSXhGREFTQmdOVkJBZ01DMUpvYjI1bExXRnNjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3DQpEZ1lEVlFRS0RBZGtiMjVsZEhScE1STXdFUVlEVlFRRERBcGtiMjVsZEhScExtWnlNSUlCSWpBTkJna3Foa2lHDQo5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBellSTHIreHZ2OHBtRVNTaVN2bjF6NkdWVEZqL1BnNVFXdjFHDQpQWVN5b1dHZTZBczJYazBJR2s1aXoxRDVvcVZ0UG9meWg5cWFXbk5TL0NVdGV6VGNEN3FsaTZ0MjM5dHVIL0s3DQo4dXN6aHVvSjBjWVhhWlNaY2FjdzBJeC9rMFpiUVdWSG9PbFNKQzhhTnNsZ0dpRzlPLytwL0ZmdTVWZi9KZTJpDQpScDh5UXExRUtyL3pHcTRyYTlESzVzMVBHTFM5L0ZEK2wwWDdsZjJzMkdPb2REbjkvaWp5dm0yVk5ZZkJVQUpsDQoxWElreHA1QUZPc2hEdjFFbHZqbG5VbzZ4R0FRVytRNWpNTFg1QXh3VmNnR3N6a1VEcFRNdmVKcDQwb1kwTmJ2DQpZYlc1c083d0xrNFpDR0RLeDNjbkRmdU5tK2RhRTg2UTdSYVk5QXlTb2crNmtwNWNyUUlEQVFBQm80R01NSUdKDQpNQjBHQTFVZERnUVdCQlRQeFo3WVBJSUZGTkVHVFpQYjVSaDk1ZXY2bURBTkJnTlZIUkVFQmpBRWdnSkdVakFKDQpCZ05WSFJNRUFqQUFNQTRHQTFVZER3RUIvd1FFQXdJRjREQWRCZ05WSFNVRUZqQVVCZ2dyQmdFRkJRY0RBUVlJDQpLd1lCQlFVSEF3SXdId1lEVlIwakJCZ3dGb0FVejhXZTJEeUNCUlRSQmsyVDIrVVlmZVhyK3Bnd0RRWUpLb1pJDQpodmNOQVFFTEJRQURnZ0VCQUhMdThiRG1ueUlZNFJwWkZqaWJhQnBBWFhydDNZM3d1TG9KVDZlaDJrdVJHZGhMDQovMXZvdkNHSjNyOWc2NjBJYk41QWtIeUZOOHZZbGthSjJ5SUhESE93MDJxTS9jSERlNi94Y1Nsb1Y0dy9BZmZ4DQpmYzdUNFpsell3ZUVzMm01bHF0d1FZRnJuT29lU0lacXl6NkYzcHhqQk0xTkhoT1NLdG95UWZIRFUwdit3aTlUDQo2cHZpbmNwUFFnZGowUTRCaWo4SG1oU2taWjUzWVd3UEJtcXIxZTl0MzBXQ2hWR0JFUzdaUkxnNDM0TG1zQS8zDQpSMktIVmZicVFEKzNxRmZKQjdQeFQxT0w4dHRXdkhVMUZ1dVZWb0k4UGJXdzhsclVhNWIyWmZIalVVcmNzQmxmDQpYRHNmQ0NyQXRkd0dZbTh6cVlMZTJKWWJTWWVwQVRIRnAzaktBWTA9DQotLS0tLUVORCBDRVJUSUZJQ0FURS0tLS0t"},"userCertificateE":{"type":"E","public":"LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0NCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBK2E5OWIweUJ1WC8rbWFDZzJUVFMNCksxVFU5Z2I0cHhQb0hYM2ZtYjNKYlFON21sMFRxYUUvbUpCaE83UTJpY1lid0M4WDlSTjczemlJY2ZVUVpJc0UNCkJraHRRVkt6OXJMbUhrZ3E3azNWVkpRZzVmTEkzbUppYmF4QVRNemZ6ZXZIdk9tWFhmcVp6UTlnNXo4cFdOT2INCmVhSHNTMkNlTzJsTTNJWUh2QVFsc3RYcjJrUHUwZ0lGNnI2QkdPVFFWeEhQRFBTbWVocWRsaHFXTUJRUVRDODQNCmZzdW9UaWxXUmwwZ2FCSFo2cy94QzlOVUkvejVLZis0dzI5UU12b290NXkxWDFvUDNZYUZseHhVTDkwaGF2anMNCk1JeE95b3ZmWlJZYWhNb0FqZ2F3TXZOTE03TVVtS1hYY1NNZ00vSGJZTWpZbm1nL3I5NDgzVHlZZGY2bFBSZnkNCmZ3SURBUUFCDQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0=","private":"LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQ0KUHJvYy1UeXBlOiA0LEVOQ1JZUFRFRA0KREVLLUluZm86IERFUy1FREUzLUNCQyxFODA2NkVBQ0FFOTlFQkZBDQoNCk9icW5LS3RYZVZNTnFQRW5BeFVVTlJNazVqdmV3dzB3RmdzYUI3dUlqUlZNOTlaeGdvaHhrcXZFVXQ3bFd5VGwNCkJURTEwNWZFQlpMZnpDdXEzYVNLNmZHL1ZWb1lLZ2YycGZkM3p2VWZ0WVFsUjRoN2VMakpBaUdHZzM1LzhHaWkNCkR3b1ZlcEloRDZIMG1CNGdIMTQybm9BUm9laUYydUc3cVNBWUoxaHg1ZWRqcUVkNXd4a2lpQ1R6TDFkaFRoTGUNCnRtL1M5enVwUExqRHd5Rk9SSUI2aEFRS1FibVEwdWpVR28ydm91aUxQaldkMXdHcTVhTW1HQmFsdUJzUmRBNlUNCmE1R2ZhUTF6a2h4Z3hGVTVuR3dOU2hWMEhiRDlhNzZuNS9kN25SalRDZ3RvL3VnN2tTcEVoMFF5Rk1mZ1E2UG0NCjNES2RLdXdDS2NaTndKRmpUQTh3bWJVazJGSC8yNDg2SjVTZXZVcVgyRm9NMk84Q0lyNVpyd01uK3pZaVBheHANCjZOOGZsRS9mMU0xcVY0VUViQXhGWlg3ZS9PeHJLbyttczhEQ2kzV2tNREZsQ1BkVWpNajU2MU5lOG81QTNZTHENCkluRUhEcGE3cUo5LzRwYXQwR3BZbGJyQmZVWGdqTEY4NnR4d2E3VldGcXREa2tLcGpGb2Fqd3NTZ1NEcldVWlgNCi9CZk5vUFlQNkNpMzdWeVNpNkp1SE9iRnR4N3RxTXpKZTJ5eGtmc1c0REJDUUtDYXhhU0ZMYzRXdkRiRjM3YVYNClJ0WWZIcDIxOUxsN2prZVhFSUhvdk51TEJySFZNQU5HWFdYbEljNm5rRWZLY2NSbTRGOUM0WEpabjVxcjlmSUoNCkJxeVdUMnUwZ0ZYMDJaTUlrKy9KeXBnY3owbGduMGJwK3ljZ3lESGtiNmlob0xhbG1GTk91RFVwSFdSMDFndFMNCmV1R1RtMGpJYnoxb3VlRENISlFTSlVZQTJvYzNFZk4wU3NydDJueE12M2ZZWkN5NlJRR0hPdEtrSFZQS2ZIUGYNCnhTNEluL0YxYzZEUkZDeXpDZ1poMFhtbzRxSnM4eDRpWTlUcDFvUDJ3Y2w1QTNMcEsrSmVIRlo1U3FScThPWXYNCngySTEyNFpqWnNiT3FEcm1hK3ZFamxhYUpDM3drZ1kyaUcxMG5jU1lYNXYzZHM1c2VHV3BjbmdISE9DUzgzS00NCmh4ZU01RE4wOHE2WCtyV1lPUjVTK0h6OHVpK1pnem9SOXY5STNJMkttNUhIR0VIc0tvRHRCZ3UwRWhKdUkxVWwNClN5eHpxZjBQVUhYVlRaM0dldkcrTk9XcDBxdlZtbURkZ2xaSlNvTkJFMDJ6VUdIeWNKT2VCeTh2dWo3S0ZBdmUNClFoaGUrbk1SaXlYa295WnZMY1BSUFBrd1NCY3Z4K3owMzJxN3A5ZVp3dUNVemJoRStmbHRMS3VEUlV0WmNSaVgNCm5aVS8wVC9hVTd0VnkrWk90M2R4c2xWNVJYdTU0d0Mzb1FJNDRjV2RtWnFKOFNDcWdlV1JrWlhIOFk0RXlrTjcNCk1lRmVSSFZRYmVudFQ4NVhqRGkyRFFWbHRiRDYySUtIenl5RnZOSWZ3Tnp0VEJUNWllUEhDVWwzNko0UGRLWDQNCnZ4bDRUNnlya2pMY0dIUjFXS2JyRnVhREJtUm1ZeVM3WXVqUmV4QXVsVGt1SW01ZHQxbWU3ci81enF1NkMxMXgNCnlUa3Q1a0VIZlFUUGs4NXJCUkhoV3gzL1g2SzNmTis0RG1rZHU3MnBpcjFTZzI2aXRuNkhzV1NndDN0WkNIOFcNCklWMFVhWEVPUXRMSHM2VlBOcVBkRmlzQncvUC9XbzBuOVhGWXZRUVU5TU5KR3UxbUJMRnBPU29kelhmZGljZDkNCjBJZ0ZrMTM5cklRamZaek9CcGNUY3d4RE82NEF0OEZPVGlVeC83SUg3Rit6UHNNZWxOQThFdmUzRUpRVkdodEcNCjFMbnhYeU80anFreWR2MlAyUmFTODZJRzdSVTlzd21Kbk9oOGg5NFlXbEtpd2xYaEtvNUVxeXovYkN2QnI5MHANCnFOVUdNZ2RFNWNYVnZKVlBld2JlTjRxTEhMTm5yUnNieEdab09nem9hWm96cXo1Vm5zU0g1QT09DQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQ==","content":"LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tDQpNSUlEMlRDQ0FzR2dBd0lCQWdJZ0FYTlB5dHlpdHh1ZlIvVWlJVXJjMGtpTmpSRlJPd21NNUFrRThCM2p5QUl3DQpEUVlKS29aSWh2Y05BUUVMQlFBd1dURUxNQWtHQTFVRUJnd0NSbEl4RkRBU0JnTlZCQWdNQzFKb2IyNWxMV0ZzDQpjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3RGdZRFZRUUtEQWRrYjI1bGRIUnBNUk13RVFZRFZRUUREQXBrDQpiMjVsZEhScExtWnlNQjRYRFRJd01UQXhOVEV3TkRZME5Wb1hEVEl6TVRBeE5qRXdORFkwTlZvd1dURUxNQWtHDQpBMVVFQmd3Q1JsSXhGREFTQmdOVkJBZ01DMUpvYjI1bExXRnNjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3DQpEZ1lEVlFRS0RBZGtiMjVsZEhScE1STXdFUVlEVlFRRERBcGtiMjVsZEhScExtWnlNSUlCSWpBTkJna3Foa2lHDQo5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBK2E5OWIweUJ1WC8rbWFDZzJUVFNLMVRVOWdiNHB4UG9IWDNmDQptYjNKYlFON21sMFRxYUUvbUpCaE83UTJpY1lid0M4WDlSTjczemlJY2ZVUVpJc0VCa2h0UVZLejlyTG1Ia2dxDQo3azNWVkpRZzVmTEkzbUppYmF4QVRNemZ6ZXZIdk9tWFhmcVp6UTlnNXo4cFdOT2JlYUhzUzJDZU8ybE0zSVlIDQp2QVFsc3RYcjJrUHUwZ0lGNnI2QkdPVFFWeEhQRFBTbWVocWRsaHFXTUJRUVRDODRmc3VvVGlsV1JsMGdhQkhaDQo2cy94QzlOVUkvejVLZis0dzI5UU12b290NXkxWDFvUDNZYUZseHhVTDkwaGF2anNNSXhPeW92ZlpSWWFoTW9BDQpqZ2F3TXZOTE03TVVtS1hYY1NNZ00vSGJZTWpZbm1nL3I5NDgzVHlZZGY2bFBSZnlmd0lEQVFBQm80R01NSUdKDQpNQjBHQTFVZERnUVdCQlQvNDlVZ0ZDb2FaekxQdWUydTFxSVBoTGgwcWpBTkJnTlZIUkVFQmpBRWdnSkdVakFKDQpCZ05WSFJNRUFqQUFNQTRHQTFVZER3RUIvd1FFQXdJRjREQWRCZ05WSFNVRUZqQVVCZ2dyQmdFRkJRY0RBUVlJDQpLd1lCQlFVSEF3SXdId1lEVlIwakJCZ3dGb0FVLytQVklCUXFHbWN5ejdudHJ0YWlENFM0ZEtvd0RRWUpLb1pJDQpodmNOQVFFTEJRQURnZ0VCQUIwbVNSNkc2YkZCRXRoWTB2RzNLN0JHdTFxbHJxeER1MlV0UXAyOGRtY0NoemViDQpBMVZJU2dRZ0dMaHJoaWlhTjNQUHpLa2RreU53djlKK2JnQzRjc09ZQXp2c2N4cndPRmZqMktkeUlWQjVFMjJlDQpnckdXaU1QZ3M3bldETGVUQVBVMFF5VzdESGEyMFNHZXFNVnQ4dG43YksyaUpmTkpUVzNoOEQ1aThiT1pFNWZWDQprQXhucWloVWVSZFhGZllOOHZwYkJPWHZPaDVyTE1iSllRZ0hSckFLeUMreFgyN0xhS2FydElNUk1BalZqeTZEDQpybFp3MlF1d2ZreUJMNWRlajFBMEpmQURBeHNENENDMWVyTjRKSStvVy9UM1BKcWlUN1FWWVQ4SlpQL1owbUJiDQpJNE5aMlVEbE8rakpRS3d5QzJFVnMxSlNOZUxUNVk1bXBTZTRiV289DQotLS0tLUVORCBDRVJUSUZJQ0FURS0tLS0t"},"userCertificateX":{"type":"X","public":"LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0NCk1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBb3hnend4V0xzSGVVY1VWY29lb0YNCkh6NnlwQ0NTYTV1Q1d3QkFaL3BYRnRmaFkrU2lxdkg2OWFmV2sxZU1SWmFsUFRkR3FXSjBWcGFQRi84TURqUFANClRGZkFnbGRzTzNDNlhIZGQ3cU5iRWNoK0E2SmZqam1rQnVWdXllbHNRR2U5Q3lLU3NHOE9GbHVnY0x2dUZoSVANClR0SnFKa1NIcXFYYnlpczA1TXBFZlZWdS8wVWNFOW9oNWYzTkk3WW1mT0VzRis5enpkUFFOeW92cnhaOGI2ZkwNCmtHelltVDcrcGR2c3kyZnJnS3kvQjBhakplaXVTSE5LdUFFM3c3VWRUeDhuNnFyMkNMelRKMTU5VklyRTJTWXANCktZa2pZZFNCTU56bFBjaEVCbFF0d2laNW91dDZDYUd1WDFVZEcvS0hoY21DbzhpNm9FWEhiR3NSamh1L2ZicXcNCkh3SURBUUFCDQotLS0tLUVORCBQVUJMSUMgS0VZLS0tLS0=","private":"LS0tLS1CRUdJTiBSU0EgUFJJVkFURSBLRVktLS0tLQ0KUHJvYy1UeXBlOiA0LEVOQ1JZUFRFRA0KREVLLUluZm86IERFUy1FREUzLUNCQyxCMzk0NDhGQUM3OTU3RUUyDQoNCmRpSGxCSk5zejJqSE4zN3pEbk5BQkVWaDZWRk5WTC80VE40QkE5SXhrK3RqL0l0NW11YmNVU2ZCQ1RNRWc5bjINCnQ2SkVLd05aSWFJc3RHS2tuSmZocURZdmxKL2hKYXVLVVhjUGthYUhtV05SMkgvNTZmNUxsZDhlL1dsVnVxdjcNCkE5VVlyRkJFbElNY3ZqRWU1RlFUcXgyTSt4RXhmUlAvdFBjNkErRUpkYk9mLzZISnQwMnN2cGhvU2M1Sk9CbFINCmZZbUZpdEROWUZsMXBHK3FZQkNTcCthY2xiZVJMZHZ1Qm41ejM0VmhDaUk3bTFDZjZGbk9mbURkN3VIemFnRXANCjRMbjdNU0xsRGZrSng2ckgySnkrVEpvM0J4cWxNaEV0WkhDV3BpREk2MHFrWDdtcWVBczVjbTlibVdOVlZaRXYNCjFISE92Nm8vZGs3clQ5YmEyUTFpWWlQeXZKR25Bc0ROSldyQXVSQXRpUmxoR2xvNFV2TFQrcHpoQmJoK2lBQkQNCndSQkl1MFdWMFUxb2NaUHN6ck0wNDJQS1pNbTNmejNYWUNqTDh1dHlHYmsxYmtlK2ZxWkVLUEtITnJreWxkSEINCmxZVHFTeG9lTGd1cUwzR0lBTW0vWm11U0tMMW9JRDBxUE5JS0xURDc0L2FwY3RqTkY1MXhRZEFBSVh2UXRhNEwNCkFlQWIycFM4OGdNT3ZhNXNLTFN3K2g1VFFZOTQzeGpjVFBLM1ZuSUJsdmhsbitQUDEzbUJLNFY1Z2V3byt3ZEUNCnErN0hkUy9zYVY2OU11V2ZDdkpyMlNtSUlDUE5iS28zS0lkQ0dJNFhtZm4rSE1QemN0SmxaYlJyaHBJclpaUWENCnF3b2NSSzJLTGV5bzJ5czdtV2xQemxubGE2UW82aCtSbWhLVXpaWlBkRG1HZS9CS1BnTWdqT1FTK1YrSmlCUTANCjZJQ2NmQVE4N3A5aGJYYlVXcE9vcm9rME9hek04K0VHZVVGanpmTGhickpFZWFWRmlGeE1pdldzS3Y0M2RnQ1YNClNlNHgzTGRMcC9VSkttRGpHQTE4THlwWHN6TkVWQ1FTSUNWVGtNN2VJaVhCTG5XSUptQ3d2NTNaNTF0QkFnSksNCmV4cGxMaVVhb2xzamx2L203cEtOb0FsLzVoWlZXZHZvL1pURHdESXhYQmIwc2srcStteXErdUs5NXA4Mm1TcG8NCjZHdVRlWjZKMThGczM4bHk3a25hUUxyOEtNcEFicnpOZFI0QnpneTVXR3hTczlPR1pIMkE1MkFZQWx2ZDF4SVoNCm9FWUVleU9ZdWFOQy9zb0E3ZytERGFwVGoxbWM1SnAvTXJURmhDTlVNc3kxL3E0TmZOdXVXbytvVC9yaUs5TTANCmJlVGxzM3hxK0ZNcktvOHg5NkVqUjd4ZkhKSzZBSkh6TndRQWFuaWttKzNhdjZoZE5uelFZMUpqODdJdDdORWMNCng4ZUFmWUVRRld5ZXFXdEFIZVF1M2FoY0lRL0d3LzNHc2ZXeHJ5TW5GK0RZZzZqS3Z1MkZDdmJNbHgyNzhxcm0NCmwyajd3dWFRK3Y4L1lITitsN1JMd2RPSDF4bXZtTldBQ21ZNUxFU0s5Ykx2MUJHN1Z0V2lSb0Y3dnpGOXlWM0gNCkdVM3g4a0RIdjBzcmlIbGZSbVc2LzlVR3ZMUFhyVnJPK1VyVHdHbDl2V3czVk1iUTNYTHV4aXl0eGRQZ2RmbkYNCnN5Z0N0VG5lbUFTOHJVZnJ0OUF3ajJyWE82MFFPU0lXSk8zT0I3OElya3k1Qm45akYwV0FQZy92WWNGNlpuUUwNCjFGbjhuZ1JLMDA0NlNRUnY1bUpMUW40N1EycDB5WHY1VzZ2bW9OLzdLRTAvUUNudHRlbElLMGxvUjY3S3dkbGINCmpuUTRsczJjTXdkTFFqVm1NczZWdnZDTkVpSjROczJicmRWSFdRUzNQTm1lK290dERoVlJBSHVoQTQxbUljc2UNCmloTWhReG56Um82R0JWTzlyeEtlNE9ZbjE1SXVJUFhKcDNBREFPUHRHcCtjeHJSVjM1NnRwSFdXUEpKZTFtZ04NCmZPcU94clBvakpNZXZya2tNbFV6SE1WRkxIVE5KbnYwUkFIaURQMjdLZFZRN2xjdFdSM2FEUT09DQotLS0tLUVORCBSU0EgUFJJVkFURSBLRVktLS0tLQ==","content":"LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tDQpNSUlEMlRDQ0FzR2dBd0lCQWdJZ0FYTlB5dHlpdHh1ZlIvVWlJVXJjMGtpTmpSRlJPd21NNUFrRThCM2p5QUl3DQpEUVlKS29aSWh2Y05BUUVMQlFBd1dURUxNQWtHQTFVRUJnd0NSbEl4RkRBU0JnTlZCQWdNQzFKb2IyNWxMV0ZzDQpjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3RGdZRFZRUUtEQWRrYjI1bGRIUnBNUk13RVFZRFZRUUREQXBrDQpiMjVsZEhScExtWnlNQjRYRFRJd01UQXhOVEV3TkRZME5Wb1hEVEl6TVRBeE5qRXdORFkwTlZvd1dURUxNQWtHDQpBMVVFQmd3Q1JsSXhGREFTQmdOVkJBZ01DMUpvYjI1bExXRnNjR1Z6TVEwd0N3WURWUVFIREFSTWVXOXVNUkF3DQpEZ1lEVlFRS0RBZGtiMjVsZEhScE1STXdFUVlEVlFRRERBcGtiMjVsZEhScExtWnlNSUlCSWpBTkJna3Foa2lHDQo5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBb3hnend4V0xzSGVVY1VWY29lb0ZIejZ5cENDU2E1dUNXd0JBDQpaL3BYRnRmaFkrU2lxdkg2OWFmV2sxZU1SWmFsUFRkR3FXSjBWcGFQRi84TURqUFBURmZBZ2xkc08zQzZYSGRkDQo3cU5iRWNoK0E2SmZqam1rQnVWdXllbHNRR2U5Q3lLU3NHOE9GbHVnY0x2dUZoSVBUdEpxSmtTSHFxWGJ5aXMwDQo1TXBFZlZWdS8wVWNFOW9oNWYzTkk3WW1mT0VzRis5enpkUFFOeW92cnhaOGI2ZkxrR3pZbVQ3K3BkdnN5MmZyDQpnS3kvQjBhakplaXVTSE5LdUFFM3c3VWRUeDhuNnFyMkNMelRKMTU5VklyRTJTWXBLWWtqWWRTQk1OemxQY2hFDQpCbFF0d2laNW91dDZDYUd1WDFVZEcvS0hoY21DbzhpNm9FWEhiR3NSamh1L2ZicXdId0lEQVFBQm80R01NSUdKDQpNQjBHQTFVZERnUVdCQlJObVpBQ1FhdHRQTUxZc05oWXBxNEVaSEVYZXpBTkJnTlZIUkVFQmpBRWdnSkdVakFKDQpCZ05WSFJNRUFqQUFNQTRHQTFVZER3RUIvd1FFQXdJRjREQWRCZ05WSFNVRUZqQVVCZ2dyQmdFRkJRY0RBUVlJDQpLd1lCQlFVSEF3SXdId1lEVlIwakJCZ3dGb0FVVFptUUFrR3JiVHpDMkxEWVdLYXVCR1J4RjNzd0RRWUpLb1pJDQpodmNOQVFFTEJRQURnZ0VCQUhVc3JhSmJ3MU5KRXY1b0RQVW9ic0RsOVhucmJHY1lRS3FRZE9tMXNWMlFLSjJ1DQp4WE1VUjlaR1g5NU8rOE5mM0kvc2NRR2Z3dlRFRzFxVnhXcU00QVI3SFFvd080NGprLzA0TTY2NmxiNnl1QTBPDQppMHl3aXVDRlM1ZHppa1gvbW8ydmF6bDBuMTVnL2tFalVGOGEvVmcrRUp5Q0tKT0pFMy9rS0lmTnRDZlhSakovDQpFblV5SnNMOEpwaVNtQ2k1S002YVpHREZaVzNva0ttQVpKdVZvaGoyaEtOS0VKODRUay9KbDZjTGpyb29SbWVSDQo3NzZFRG5iemo2MU44VWhlYUJydUlTY3RBejYvWVRMcVpkekJrS21NNW5jV211cXdUOXE5a21LeDNUZThUYmxoDQpRSmx3THNVWFVUem44ZU9OSHNraktRaWtLMzlsai9kL0c2bzhmVGM9DQotLS0tLUVORCBDRVJUSUZJQ0FURS0tLS0t"}}',
                true
            ),
            ''
        );
        self::assertSame('PHxazzDE2/S/oUfwiboAxFIiI9AGgtPhSSgpiqdobMs=', (new BankPublicKeyDigest())->__invoke($tmp->getBankCertificateE()));
    }
}
