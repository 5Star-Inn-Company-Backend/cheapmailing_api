
import { useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { Actions } from "../../../../../components/actions";
import Spinner from "../../../../../components/spinner/spinner";
import { Tag_SliceActions } from "../../../../../store/tagSlice";
import { NoData } from "../../../../../components/nodata";

export const TagContainer =()=>{
    const navigate = useNavigate()
    const dispatch = useDispatch()
    const tag = useSelector(
        state => state.tag
    )
    const[
        itemToDelete,
        setItemToDelete
    ]=useState([])

    if(tag.GetTagsStatus ==='pending'){
        return <Spinner/>
    }
    const handleChange=(e,{id})=>{
        const newArray = itemToDelete.filter(item=>item!==id)
        setItemToDelete((prevState)=>{
            if(e.target.checked){
                return[
                ...prevState,
                    id
                ]
            }else{
                return newArray
            }
        })
        
    }

    const handleSelectChange=(e)=>{
        if(e.target.value ==="Name"){
            dispatch(Tag_SliceActions.sortDataByName())
        }else{
            dispatch(Tag_SliceActions.sortDataByCreatedAt())
        }
    }

    const handleInputChange=(e)=>{
        dispatch(Tag_SliceActions.searchdata(e.target.value))
    }

    return(
        <>
        <Actions
            actionName="Add Tag"
            deleteArray={itemToDelete}
            handleChange={handleSelectChange}
            handleInputChange={handleInputChange}
        />
        <div className="w-overflow">
            <table className=" table table-striped table-hover table-bordered table-responsive caption-top mb-3">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"></th>
                        <th scope="col">Name</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Updated At</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        tag
                            .Tags?.map((tag,index)=>{
                                const{
                                    name,
                                    updated_at,
                                    created_at,
                                    id
                                } = tag
                                return(
                                    <tr key={index}>
                                        <th scope="row">{index+1}</th>
                                        <td>
                                            <input 
                                                className="darkform-check-input p-2 border border-white rounded form-check-input me-1"
                                                type="checkbox"
                                                onChange={(e)=>handleChange(e,{id})}
                                            />
                                        </td>
                                        <td>{name}</td>
                                        <td>{
                                                new Date(created_at)
                                                .toLocaleString()
                                            }
                                        </td>
                                        <td>{
                                                new Date(updated_at)
                                                .toLocaleString()
                                            }</td>
                                        <td>
                                            <div className="d-flex align-items-center">
                                                <div className="dropdown">
                                                    <button 
                                                        className="btn btn-secondary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false" 
                                                    >
                                                    </button>
                                                    <ul className="dropdown-menu">
                                                        <li
                                                            className="dropdown-item"
                                                            onClick={()=>navigate(`/user/tag/update/${id}`)}
                                                        >
                                                           Update
                                                        </li>
                                                        <li
                                                            className="dropdown-item"
                                                        >
                                                            Delete
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                )
                            }
                        )
                    }
                    
                </tbody>
            </table>
        </div>
       
        {
            tag
            .Tags.length === 0 && <NoData/>
        }
        </>
    )
}