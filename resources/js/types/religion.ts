export const RELIGIONS = [
  'muslim',
  'catholicism',
  'christianity',
  'hindu',
  'buddhism',
  'confucianism',
  'other',
] as const;

export type Religion = (typeof RELIGIONS)[number];

export const RELIGION_TRANSLATIONS: Record<Religion, string> = {
  muslim: 'Islam',
  catholicism: 'Katolik',
  christianity: 'Kristen',
  hindu: 'Hindu',
  buddhism: 'Buddha',
  confucianism: 'Konghucu',
  other: 'Lainnya',
};
