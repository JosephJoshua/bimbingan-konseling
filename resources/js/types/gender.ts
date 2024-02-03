export const GENDERS = ['male', 'female'] as const;

export type Gender = (typeof GENDERS)[number];

export const GENDER_TRANSLATIONS: Record<Gender, string> = {
  male: 'Pria',
  female: 'Wanita',
};
