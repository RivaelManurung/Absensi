import { Link } from "react-router-dom";
import { Button } from "../../../components/ui/button";
import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { useToast } from "../../../app/providers/ToastProvider";
import { BarcodesFilters } from "../components/BarcodesFilters";
import { BarcodesTable } from "../components/BarcodesTable";
import { useBarcodes } from "../hooks/useBarcodes";
import { getApiErrorMessage } from "../../../lib/apiError";
import { barcodeService } from "../services/barcodeService";
import type { BarcodeListFilters } from "../types/barcode.types";

export function BarcodesIndexPage() {
  const [filters, setFilters] = useState<BarcodeListFilters>({ query: "", status: "all" });
  const listQuery = useBarcodes(filters);
  const queryClient = useQueryClient();
  const toast = useToast();

  const refresh = async () => {
    await queryClient.invalidateQueries({ queryKey: ["barcodes"] });
  };

  const regenerate = useMutation({
    mutationFn: barcodeService.regenerate,
    onSuccess: () => {
      toast.success("Barcode berhasil diregenerate.");
      void refresh();
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal regenerate barcode."));
    },
  });

  const deactivate = useMutation({
    mutationFn: barcodeService.deactivate,
    onSuccess: () => {
      toast.success("Barcode berhasil dinonaktifkan.");
      void refresh();
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal menonaktifkan barcode."));
    },
  });

  const remove = useMutation({
    mutationFn: barcodeService.remove,
    onSuccess: () => {
      toast.success("Barcode berhasil dihapus.");
      void refresh();
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal menghapus barcode."));
    },
  });

  return (
    <section className="space-y-6">
      <header className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-semibold">Barcode List</h1>
        <Link to="/barcodes/generate"><Button>Generate Barcode</Button></Link>
      </header>

      <BarcodesFilters value={filters} onChange={setFilters} />

      {listQuery.isLoading ? (
        <p className="text-sm text-muted-foreground">Loading barcodes...</p>
      ) : (
        <BarcodesTable
          rows={listQuery.data ?? []}
          onRegenerate={(id) => regenerate.mutate(id)}
          onDeactivate={(id) => deactivate.mutate(id)}
          onDelete={(id) => remove.mutate(id)}
        />
      )}
    </section>
  );
}
