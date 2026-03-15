import { useQuery } from "@tanstack/react-query";
import { barcodeService } from "../services/barcodeService";
import type { BarcodeListFilters } from "../types/barcode.types";

export function useBarcodes(filters: BarcodeListFilters) {
  return useQuery({
    queryKey: ["barcodes", filters],
    queryFn: () =>
      barcodeService.getBarcodes({
        query: filters.query,
        status: filters.status,
      }),
  });
}
