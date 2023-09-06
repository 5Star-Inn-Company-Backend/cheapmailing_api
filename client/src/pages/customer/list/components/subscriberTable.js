import {useSelector } from "react-redux";
import Spinner from "../../../../components/spinner/spinner";
import { Actions } from "./subAction";
import { NoData } from "../../../../components/nodata";
import { DeleteSubscriber } from "../../../../store/subscriberSlice";
import { DeleteBtn } from "../../template/components/deleteBtn";
import { ViewportList } from "react-viewport-list";
import { useRef } from "react";
export const SubscriberTable=({
    content,
    setModalBody,
    setCreateType,
    hidemodal
})=>{
        const subsriber = useSelector(
            state => state.subscriber
        )
        const listRef = useRef(null)
        if(subsriber.GetSubscribersStatus ==='pending'){
            return <Spinner/>
        }

    return(
        <>
            <Actions
                setModalBody={setModalBody}
                setCreateType={setCreateType}
                hidemodal={hidemodal}
            />
            <div className="w-overflow" ref={listRef}>
                <table className="table table-striped table-hover table-bordered table-responsive caption-top mb-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Email</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Country</th>
                            <th scope="col">State</th>
                            <th scope="col">Phone</th>
                            <th scope="col">DOB</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <ViewportList viewportRef={listRef} items={content?.subscribers} margin={8}>
                        {
                             ((sub,index)=>{
                                    const{
                                        email,
                                        fname,
                                        lname,
                                        country,
                                        state,
                                        phone,
                                        dob,
                                        id
                                    }=sub;
                                    return(
                                        <tr key={index}>
                                            <th scope="row" className="fs-6">{index+1}</th>
                                            <td className="fs-6">{email}</td>
                                            <td className="fs-6">{fname}</td>
                                            <td className="fs-6">{lname}</td>
                                            <td className="fs-6">{country}</td>
                                            <td className="fs-6">{state}</td>
                                            <td className="fs-6">{phone}</td>
                                            <td className="fs-6">{dob}</td>
                                            <td>
                                                <div className="dropdown">
                                                    <button 
                                                        className="btn btn-secondary dropdown-toggle"
                                                        type="button" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false" 
                                                    >
                                                    </button>
                                                    <ul className="dropdown-menu">
                                                        <DeleteBtn
                                                            status = {content.deleteSubStatus}
                                                            deleteFunc ={DeleteSubscriber({id})}
                                                        />
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    )
                                })
                        }
                        </ViewportList>
                    </tbody>
                </table>
            </div>
            {
                content
                .subscribers?.length === 0 && <NoData/>
            }
        </>
    )
}