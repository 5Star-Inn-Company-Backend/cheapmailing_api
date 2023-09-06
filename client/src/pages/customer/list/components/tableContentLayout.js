import { Modal } from "../../../../components/modal/modal";

export const Tablelayout=({
    title,
    subNumber,
    titleIcon,
    tableContainer,
    modalBody,
    modalTitle,
    hidemodal
})=>{
    
    return(
        <>
            <div className="mb-3 pb-2 bg-lightBlue pdx-4">
                <div className="d-flex align-items-center">
                    <span className="me-2">
                        {titleIcon}
                    </span>
                    <div className="fs-4">
                    {title}
                    </div>
                </div>
                {
                    subNumber &&(
                        <p className="badge bg bg-slate-grey">
                            {subNumber}
                        </p>
                    )
                }
            </div>
            <div className="pdx-4 w-overflow">
                {tableContainer}
            </div>
            <Modal
                title={modalTitle}
                body={modalBody}
                hidemodal={hidemodal}
            />
        </>
    )
}