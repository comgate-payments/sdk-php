<?php declare(strict_types = 1);

namespace Comgate\SDK\Entity\Codes;

final class PaymentMethodCode
{

	public const ALL = 'ALL';
	public const ALL_CARDS = 'CARD_ALL';
	public const ALL_BANKS = 'BANK_ALL';
	public const BANK_ONLY = 'BANK_ONLY';
	public const LATER_ALL = 'LATER_ALL';
	public const PART_ALL = 'PART_ALL';
	public const LOAN_ALL = 'LOAN_ALL';
	public const APPLEPAY_REDIRECT = 'APPLEPAY_REDIRECT';
	public const GOOGLEPAY_REDIRECT = 'GOOGLEPAY_REDIRECT';
	public const PAYPAL = 'PAYPAL';

	// CZ

	public const CARD_CARD_CZ_CSOB_2 = 'CARD_CZ_CSOB_2';
	public const CARD_CARD_CZ_COMGATE = 'CARD_CZ_COMGATE';
	public const CARD_CARD_CZ_BS = 'CARD_CZ_BS';

	public const BANK_AIRBANK_TRANSFER = 'BANK_CZ_AB';
	public const BANK_CSOB_TRANSFER = 'BANK_CZ_CSOB';
	public const BANK_OTHER_TRANSFER = self::BANK_OTHER_CZ_TRANSFER;
	public const BANK_OTHER_CZ_TRANSFER = 'BANK_CZ_OTHER';
	public const BANK_RB_BUTTON = 'BANK_CZ_RB';
	public const BANK_KB_BUTTON = 'BANK_CZ_KB';
	public const BANK_MONETA_BUTTON = 'BANK_CZ_MO';
	public const BANK_FIO_BUTTON = 'BANK_CZ_FB_P';
	public const BANK_CESKASPORITELNA_BUTTON = self::BANK_CESKASPORITELNA_BUTTON_PSD2; // již neaktivní (přesměrování na metodu BANK_CZ_CS_PSD2)
	public const BANK_MBANK_BUTTON = 'BANK_CZ_MB_P';
	public const BANK_CSOB_BUTTON = 'BANK_CZ_CSOB_P';
	public const BANK_UNICREDIT_BUTTON = 'BANK_CZ_UC';
	public const BANK_KB_BUTTON_PSD2 = 'BANK_CZ_KB_PSD2';
	public const BANK_AIRBANK_BUTTON_PSD2 = 'BANK_CZ_AB_PSD2';
	public const BANK_CSOB_BUTTON_PSD2 = 'BANK_CZ_CSOB_PSD2';
	public const BANK_CESKASPORITELNA_BUTTON_PSD2 = 'BANK_CZ_CS_PSD2';
	public const BANK_FIO_BUTTON_PSD2 = 'BANK_CZ_FB_PSD2';
	public const BANK_MBANK_BUTTON_PSD2 = 'BANK_CZ_MB_PSD2';
	public const BANK_MONETA_BUTTON_PSD2 = 'BANK_CZ_MO_PSD2';
	public const BANK_RB_BUTTON_PSD2 = 'BANK_CZ_RB_PSD2';
	public const BANK_UNICREDIT_BUTTON_PSD2 = 'BANK_CZ_UC_PSD2'; // tatím neaktivní
	public const BANK_PARTNERS_BUTTON_PSD2 = 'BANK_CZ_PB_PSD2';
	public const LATER_TWISTO = 'LATER_TWISTO';
	public const LATER_SKIPPAY = 'LATER_SKIPPAY';
	public const LATER_PLATIMPAK = 'LATER_PLATIMPAK';
	public const LATER_COFIDIS = 'LATER_COFIDIS'; // zatím neaktivní
	public const PART_TWISTO = 'PART_TWISTO';
	public const PART_SKIPPAY = 'PART_SKIPPAY';
	public const PART_ESSOX = 'PART_ESSOX';
	public const PART_COFIDIS = 'PART_COFIDIS'; // zatím neaktivní
	public const LOAN_HOMECREDIT = 'LOAN_HOMECREDIT';
	public const LOAN_ESSOX = 'LOAN_ESSOX';
	public const LOAN_COFIDIS = 'LOAN_COFIDIS';

	// EUR

	public const BANK_OTHER_EUR_TRANSFER = 'BANK_EUR_OTHER';

	// SK

	public const LOAN_TB = 'LOAN_TB';
	public const BANK_OTHER_SK_TRANSFER = 'BANK_SK_OTHER';
	public const BANK_VUB_SK_BUTTON = 'BANK_SK_VUB_P';
	public const BANK_TATRA_SK_BUTTON = 'BANK_SK_TB_P';
	public const BANK_CSOB_SK_BUTTON = self::BANK_SK_CSOB_SK_BUTTON_PSD2; // již neaktivní (přesměrováno na BANK_SK_CSOB_PSD2)
	public const BANK_SLOVENSKASPORITELNA_SK_BUTTON = 'BANK_SK_SLSP';
	public const BANK_SK_CSOB_SK_BUTTON_PSD2 = 'BANK_SK_CSOB_PSD2';
	public const BANK_SK_RB_SK_BUTTON_PSD2 = 'BANK_SK_RB_PSD2';
	public const BANK_SK_SLOVENSKASPORITELNA_SK_BUTTON_PSD2 = 'BANK_SK_SLSP_PSD2';
	public const BANK_SK_TATRA_SK_BUTTON_PSD2 = 'BANK_SK_TB_PSD2';
	public const BANK_SK_UNICREDIT_SK_BUTTON_PSD2 = 'BANK_SK_UC_PSD2';
	public const BANK_SK_VUB_SK_BUTTON_PSD2 = 'BANK_SK_VUB_PSD2';
	public const PART_SK_SKIPPAY = 'PART_SK_SKIPPAY';


	// PL

	public const BANK_PL_ALR = 'BANK_PL_ALR';
	public const BANK_PL_BGZ = 'BANK_PL_BGZ';
	public const BANK_PL_BL = 'BANK_PL_BL';
	public const BANK_PL_BM = 'BANK_PL_BM';
	public const BANK_PL_BOS = 'BANK_PL_BOS';
	public const BANK_PL_BP = 'BANK_PL_BP';
	public const BANK_PL_BSP = 'BANK_PL_BSP';
	public const BANK_PL_BZ = 'BANK_PL_BZ';
	public const BANK_PL_CA = 'BANK_PL_CA';
	public const BANK_PL_GO = 'BANK_PL_GO';
	public const BANK_PL_ING = 'BANK_PL_ING';
	public const BANK_PL_INT = 'BANK_PL_INT';
	public const BANK_PL_MB = 'BANK_PL_MB';
	public const BANK_PL_NEB = 'BANK_PL_NEB';
	public const BANK_PL_NOB = 'BANK_PL_NOB';
	public const BANK_PL_PB = 'BANK_PL_PB';
	public const BANK_PL_PEK = 'BANK_PL_PEK';
	public const BANK_PL_PKO = 'BANK_PL_PKO';
	public const BANK_PL_TOB = 'BANK_PL_TOB';
	public const BANK_PL_VWB = 'BANK_PL_VWB';

	public const SELF = [
		self::ALL,
		self::ALL_CARDS,
		self::ALL_BANKS,
		self::BANK_ONLY,
		self::LATER_ALL,
		self::PART_ALL,
		self::LOAN_ALL,
		self::APPLEPAY_REDIRECT,
		self::GOOGLEPAY_REDIRECT,
		self::PAYPAL,
		self::CARD_CARD_CZ_CSOB_2,
		self::CARD_CARD_CZ_COMGATE,
		self::CARD_CARD_CZ_BS,
		self::BANK_AIRBANK_TRANSFER,
		self::BANK_CSOB_TRANSFER,
		self::BANK_OTHER_TRANSFER,
		self::BANK_OTHER_CZ_TRANSFER,
		self::BANK_RB_BUTTON,
		self::BANK_KB_BUTTON,
		self::BANK_MONETA_BUTTON,
		self::BANK_FIO_BUTTON,
		self::BANK_CESKASPORITELNA_BUTTON,
		self::BANK_MBANK_BUTTON,
		self::BANK_CSOB_BUTTON,
		self::BANK_UNICREDIT_BUTTON,
		self::BANK_KB_BUTTON_PSD2,
		self::BANK_AIRBANK_BUTTON_PSD2,
		self::BANK_CSOB_BUTTON_PSD2,
		self::BANK_CESKASPORITELNA_BUTTON_PSD2,
		self::BANK_FIO_BUTTON_PSD2,
		self::BANK_MBANK_BUTTON_PSD2,
		self::BANK_MONETA_BUTTON_PSD2,
		self::BANK_RB_BUTTON_PSD2,
		self::BANK_UNICREDIT_BUTTON_PSD2,
		self::BANK_PARTNERS_BUTTON_PSD2,
		self::LATER_TWISTO,
		self::LATER_SKIPPAY,
		self::LATER_PLATIMPAK,
		self::LATER_COFIDIS,
		self::PART_TWISTO,
		self::PART_SKIPPAY,
		self::PART_ESSOX,
		self::PART_COFIDIS,
		self::LOAN_HOMECREDIT,
		self::LOAN_ESSOX,
		self::LOAN_COFIDIS,
		self::LOAN_TB,
		self::BANK_OTHER_SK_TRANSFER,
		self::BANK_OTHER_EUR_TRANSFER,
		self::BANK_VUB_SK_BUTTON,
		self::BANK_TATRA_SK_BUTTON,
		self::BANK_CSOB_SK_BUTTON,
		self::BANK_SLOVENSKASPORITELNA_SK_BUTTON,
		self::BANK_SK_CSOB_SK_BUTTON_PSD2,
		self::BANK_SK_RB_SK_BUTTON_PSD2,
		self::BANK_SK_SLOVENSKASPORITELNA_SK_BUTTON_PSD2,
		self::BANK_SK_TATRA_SK_BUTTON_PSD2,
		self::BANK_SK_UNICREDIT_SK_BUTTON_PSD2,
		self::BANK_SK_VUB_SK_BUTTON_PSD2,
		self::PART_SK_SKIPPAY,
		self::BANK_PL_ALR,
		self::BANK_PL_BGZ,
		self::BANK_PL_BL,
		self::BANK_PL_BM,
		self::BANK_PL_BOS,
		self::BANK_PL_BP,
		self::BANK_PL_BSP,
		self::BANK_PL_BZ,
		self::BANK_PL_CA,
		self::BANK_PL_GO,
		self::BANK_PL_ING,
		self::BANK_PL_INT,
		self::BANK_PL_MB,
		self::BANK_PL_NEB,
		self::BANK_PL_NOB,
		self::BANK_PL_PB,
		self::BANK_PL_PEK,
		self::BANK_PL_PKO,
		self::BANK_PL_TOB,
		self::BANK_PL_VWB,
	];

}
