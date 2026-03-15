export type BarcodeRecord = {
  id: string;
  employee_id: string;
  employee_name?: string | null;
  employee_code?: string | null;
  code: string;
  type: string;
  is_active: boolean;
  generated_at: string;
  generated_by_name?: string | null;
  qr_image_data_url?: string;
};

export type BarcodeListFilters = {
  query: string;
  status: "all" | "active" | "inactive";
};
