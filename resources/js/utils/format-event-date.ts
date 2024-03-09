import { Event } from '@/types/event';
import { format, isSameDay } from 'date-fns';

const formatEventDate = (event: Event) => {
  if (isSameDay(event.start_date, event.end_date)) {
    return (
      format(event.start_date, 'd MMM y HH:mm') +
      ' - ' +
      format(event.end_date, 'HH:mm')
    );
  }

  return (
    format(event.start_date, 'd MMM y HH:mm') +
    ' - ' +
    format(event.end_date, 'd MMM y HH:mm')
  );
};

export default formatEventDate;
