import React, {FC} from "react";

const Table: FC = ({children}) => {
    return (
        <table className="form-table">
            <tbody>
            {children}
            </tbody>
        </table>
    );
}

export const TableRow: FC<{label: string}> = ({label, children}) => {
    return (
        <tr>
            <th scope="row">
                <label>{label}</label>
            </th>
            <td>
                {children}
            </td>
        </tr>
    )
}

export default Table;