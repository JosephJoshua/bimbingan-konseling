export const STATUSES_IN_FAMILY = [
  'biological',
  'adopted',
  'stepchild',
] as const;

export type StatusInFamily = (typeof STATUSES_IN_FAMILY)[number];

export const STATUS_IN_FAMILY_TRANSLATIONS: Record<StatusInFamily, string> = {
  biological: 'Anak Kandung',
  adopted: 'Anak Angkat',
  stepchild: 'Anak Tiri',
};
