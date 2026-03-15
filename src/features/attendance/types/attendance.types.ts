export type AttendanceRecord = {
  id: string;
  employee_id: string;
  attendance_date: string;
  check_in_time?: string | null;
  check_out_time?: string | null;
  check_in_method?: string | null;
  check_out_method?: string | null;
  status?: string | null;
};

export type AttendanceLogRecord = {
  id: string;
  attendance_id: string;
  type: string;
  device_info?: string | null;
};

export type AttendanceListItem = {
  id: string;
  employee: string;
  date: string;
  checkIn: string;
  checkOut: string;
  device: string;
  status: string;
};

export type AttendanceFilters = {
  date: string;
  query: string;
};
