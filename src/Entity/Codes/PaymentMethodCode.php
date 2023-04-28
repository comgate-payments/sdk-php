<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class PaymentMethodCode
{

	public const ALL = 'ALL';
	public const ALL_CARDS = 'CARD_ALL';
	public const ALL_BANKS = 'BANK_ALL';

	// TODO googlepay, applepay

	// TODO LATER_SKIPPAY, PART_SKIPPAY

	// CZ

	public const CARD_CARD_CZ_CSOB_2 = 'CARD_CZ_CSOB_2';
	public const CARD_CARD_CZ_BS = 'CARD_CZ_BS';

	public const BANK_AIRBANK_TRANSFER = 'BANK_CZ_AB';
	public const BANK_CSOB_TRANSFER = 'BANK_CZ_CSOB';
	public const BANK_OTHER_TRANSFER = 'BANK_CZ_OTHER';

	public const BANK_RB_BUTTON = 'BANK_CZ_RB';
	public const BANK_KB_BUTTON = 'BANK_CZ_KB';
	public const BANK_MONETA_BUTTON = 'BANK_CZ_MO';
	public const BANK_FIO_BUTTON = 'BANK_CZ_FB_P';
	public const BANK_CESKASPORITELNA_BUTTON = 'BANK_CZ_CS_P';
	public const BANK_MBANK_BUTTON = 'BANK_CZ_MB_P';
	public const BANK_CSOB_BUTTON = 'BANK_CZ_CSOB_P';
	public const BANK_UNICREDIT_BUTTON = 'BANK_CZ_UC';

	// SK
	public const BANK_OTHER_SK_TRANSFER = 'BANK_SK_OTHER';
	public const BANK_VUB_SK_BUTTON = 'BANK_SK_VUB';
	public const BANK_TATRA_SK_BUTTON = 'BANK_SK_TB';
	public const BANK_CSOB_SK_BUTTON = 'BANK_SK_CSOB';
	public const BANK_SLOVENSKASPORITELNA_SK_BUTTON = 'BANK_SK_SLSP';

	// TODO doplnit další banky

	public const SELF = [
		self::ALL,
		self::ALL_CARDS,
		self::ALL_BANKS,
		self::BANK_AIRBANK_TRANSFER,
		self::BANK_CSOB_TRANSFER,
		self::BANK_OTHER_TRANSFER,
		self::BANK_RB_BUTTON,
		self::BANK_KB_BUTTON,
		self::BANK_MONETA_BUTTON,
		self::BANK_FIO_BUTTON,
		self::BANK_CESKASPORITELNA_BUTTON,
		self::BANK_MBANK_BUTTON,
		self::BANK_CSOB_BUTTON,
		self::BANK_UNICREDIT_BUTTON,
		self::BANK_OTHER_SK_TRANSFER,
		self::BANK_SLOVENSKASPORITELNA_SK_BUTTON,
		self::BANK_VUB_SK_BUTTON,
		self::BANK_TATRA_SK_BUTTON,
		self::BANK_CSOB_SK_BUTTON,
	];

}
