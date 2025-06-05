import { ChevronDown, X } from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from "@/components/ui/dropdown-menu";
import { ReactNode } from "react";

type OptionType = {
    id: number | string;
    name: string;
};

export default function DropdownSelect<T extends OptionType>({
                                                                 label,
                                                                 options,
                                                                 selectedOption,
                                                                 setSelectedOption,
                                                                 placeholder = "Pilih...",
                                                                 icon,
                                                             }: {
    label: string;
    options: T[];
    selectedOption: T | null;
    setSelectedOption: (option: T | null) => void;
    placeholder?: string;
    icon?: ReactNode;
}) {
    return (
        <div className="w-full max-w-xs space-y-1 small-font-size">
            <label className="font-medium text-foreground">{label}</label>

            <DropdownMenu>
                <DropdownMenuTrigger className="w-full flex items-center justify-between px-4 py-2 border rounded-md bg-background text-foreground shadow-sm hover:bg-muted focus:outline-none">
                    <div className="flex items-center gap-2 pe-1">
                        {icon}
                        <span>{selectedOption ? selectedOption.name : placeholder}</span>
                    </div>
                    <ChevronDown size={16} />
                </DropdownMenuTrigger>

                <DropdownMenuContent className="w-full max-w-xs mt-2 p-1">
                    {options.map((option) => (
                        <DropdownMenuItem
                            key={option.id}
                            onClick={() => setSelectedOption(option)}
                            className="cursor-pointer"
                        >
                            {option.name}
                        </DropdownMenuItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>

            {/* External clear button, shown only when selection exists */}
            {selectedOption && (
                <button
                    type="button"
                    onClick={() => setSelectedOption(null)}
                    className="text-muted-foreground hover:text-foreground flex items-center gap-1 mt-1"
                >
                    <X size={14} />
                    Kosongkan pilihan
                </button>
            )}
        </div>
    );
}
