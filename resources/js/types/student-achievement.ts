export type StudentAchievement = {
  id: number;
  student_id: number;
  title: string;
};

export type WithStudentAchievements<T> = T & {
  achievements: StudentAchievement[];
};
