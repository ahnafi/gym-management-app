import { useState, useMemo, ReactNode } from "react";
import { Search, X } from "lucide-react";
// import { Laboratory_Simple, SimpleOption } from "@/types";

type OptionType = {
    id: number | string;
    name: string;
};

export default function SearchableSelect<T extends OptionType>({
                                                                   label,
                                                                   options,
                                                                   selectedOption,
                                                                   setSelectedOption,
                                                                   placeholder = "Cari...",
                                                                   searchIcon = <Search size={14} />,
                                                               }: {
    label: string;
    options: T[];
    selectedOption: T | null;
    setSelectedOption: (option: T | null) => void;
    placeholder?: string;
    searchIcon?: ReactNode;
}) {
    const [query, setQuery] = useState("");

    const filteredOptions = useMemo(() => {
        return options.filter((option) =>
            option.name.toLowerCase().includes(query.toLowerCase())
        );
    }, [query, options]);

    const handleSelect = (option: T) => {
        setSelectedOption(option);
        setQuery(option.name);
    };

    const handleClear = () => {
        setSelectedOption(null);
        setQuery("");
    };

    return (
        <div className="w-full max-w-xs space-y-1 small-font-size">
            <label className="font-medium text-foreground">{label}</label>
            <div className="relative">
        <span className="absolute left-2 top-1/2 -translate-y-1/2 text-muted-foreground">
          {searchIcon}
        </span>
                <input
                    type="text"
                    className="w-full pl-8 pr-3 py-2 shadow-sm rounded-md border border-muted bg-background text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
                    placeholder={placeholder}
                    value={query}
                    onChange={(e) => {
                        setQuery(e.target.value);
                        setSelectedOption(null); // optional: clear selection while typing
                    }}
                />

                {!selectedOption && query && (
                    <div className="absolute z-10 mt-1 w-full max-h-36 overflow-auto border border-muted rounded bg-background shadow-sm">
                        {filteredOptions.length > 0 ? (
                            filteredOptions.map((option) => (
                                <div
                                    key={option.id}
                                    className="px-3 py-1.5 hover:bg-muted cursor-pointer "
                                    onClick={() => handleSelect(option)}
                                >
                                    {option.name}
                                </div>
                            ))
                        ) : (
                            <div className="px-3 py-2 text-muted-foreground ">
                                Tidak ditemukan
                            </div>
                        )}
                    </div>
                )}
            </div>

            {/* Dipilih + Clear Button */}
            {selectedOption && (
                <div className="flex items-center gap-1 text-muted-foreground mt-1">
          <span>
            Dipilih: <strong>{selectedOption.name}</strong>
          </span>
                    <button
                        type="button"
                        onClick={handleClear}
                        className="text-muted-foreground hover:text-foreground"
                    >
                        <X size={14} />
                    </button>
                </div>
            )}
        </div>
    );
}
