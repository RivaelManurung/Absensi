import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useToast } from "../../../app/providers/ToastProvider";
import { Button } from "../../../components/ui/button";
import { getApiErrorMessage } from "../../../lib/apiError";
import { APP_ROUTES } from "../../../lib/constants";
import { employeeService } from "../../employees/services/employeeService";
import { barcodeService } from "../services/barcodeService";

export function BarcodesGeneratePage() {
  const [employeeId, setEmployeeId] = useState("");
  const queryClient = useQueryClient();
  const navigate = useNavigate();
  const toast = useToast();

  const employeesQuery = useQuery({
    queryKey: ["employee-options"],
    queryFn: () => employeeService.getEmployees(),
  });

  const createMutation = useMutation({
    mutationFn: (selectedEmployeeId: string) => barcodeService.generate(selectedEmployeeId),
    onSuccess: async (created) => {
      await queryClient.invalidateQueries({ queryKey: ["barcodes"] });
      toast.success("Barcode berhasil dibuat.");
      navigate(`/barcodes/${created.id}`);
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal membuat barcode."));
    },
  });

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Barcode Generator</h1>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-3 text-lg font-semibold">Employee Selection</h2>
        <select
          className="h-10 w-full rounded-xl border border-border bg-input px-3 text-sm md:max-w-md"
          value={employeeId}
          onChange={(event) => setEmployeeId(event.target.value)}
        >
          <option value="">Select Employee</option>
          {(employeesQuery.data ?? []).map((employee) => (
            <option key={employee.id} value={employee.id}>
              {employee.code} - {employee.name}
            </option>
          ))}
        </select>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-3 text-lg font-semibold">Barcode Preview</h2>
        <div className="grid gap-3 md:grid-cols-2">
          <div className="flex h-36 items-center justify-center rounded-xl border border-dashed border-border text-muted-foreground">
            Barcode akan muncul setelah generate
          </div>
          <div className="space-y-2 text-sm">
            <p><span className="text-muted-foreground">Status:</span> Pilih employee lalu klik Generate Barcode</p>
          </div>
        </div>
      </section>

      <div className="flex flex-wrap gap-3">
        <Button onClick={() => createMutation.mutate(employeeId)} disabled={employeeId.length === 0 || createMutation.isPending}>
          {createMutation.isPending ? "Generating..." : "Generate Barcode"}
        </Button>
        <Button variant="outline" onClick={() => navigate(APP_ROUTES.barcodes)}>
          Back to List
        </Button>
      </div>
    </section>
  );
}
